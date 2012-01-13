<?php
/**
* @Autho Shohei Yokoyama
* @Date: 5/6/11
* @comments Multi-Dimension KMeans based on work by Jason Cowdy & Katie Zagorski
*/



/**
* Clusters points using the kmeans clustering algorithm
* @param array $data the data points to cluster
* @param int $k The number of clusters to use 
* @return array A mixed array contiaining an array of centroids, and k arrays containing clusters and the indeces of the points it contains
*/
function kmeans($data, $k) {
	if($k <= 0)
	{
		echo "ERROR: K must be a positive integer greater than 0";
		exit(0);
	}
	if(count($data) == 0 or !is_array($data[1])){
		echo "ERROR: data must be a array(array(axis1,axis2,axis3...),...)";
		exit(0);
	}
        $oldCentroids = randomCentroids($data, $k);
	while (true)
	{
		$clusters = assign_points($data, $oldCentroids, $k);
		$newCentroids = calcCenter($clusters, $data);
		if(issame($oldCentroids,$newCentroids))
		{
			return(array ("centroids" => $newCentroids, "clusters" => $clusters));
		}
		$oldCentroids = $newCentroids;
	}
}


/**
* Calculate cluster's center
* @param array $clusters k-clusters
* @param array $data the data points to cluster
* @return array cluster's center
*/
function calcCenter($clusters, $data)
{
	foreach($clusters as $num_cluster => $cluster_elements)
	{
		foreach ($cluster_elements as $cluster_element)
		{
			$cluster_elements_coords[$num_cluster][] = $data[$cluster_element];
		}
	}
	foreach ($cluster_elements_coords as $cluster_element_coords)
	{
		$cluster_centers[] = recenter($cluster_element_coords);
	}
	return $cluster_centers;
}



/**
* Calculates the center coordinates of a set of points
* @param array $coords An array of x and y points
* @return array An array containing the x and y coordinates of the center point
*/
function recenter($coords)
{
	$dim = count($coords[0]);
	$axis = array();
	foreach ($coords as $k)
	{
		for($a = 0; $a < $dim; $a++){
			if(!isset($axis[$a])){
				$axis[$a] = 0;
			}
			$axis[$a] += $k[$a];
		}
	}
	for($a = 0; $a < $dim; $a++){
		$center[$a] = round($axis[$a] / count($coords),2);
	}
	return $center;
}



/**
* Calculates the distance between two points
* @param array $v1 An integer array with x and y coordinate values
* @param array $v2 An integer array with x and y coordinate values
* @return double The distance between the two points
*/
function dist($v1, $v2)
{
	$dim = count($v1);
	$d = array();
	for($a = 0; $a < $dim; $a++){
		$d[] = pow(abs($v1[$a] - $v2[$a]),2);
	}
	return round(sqrt(array_sum($d)),2);
}



/**
* return true if $v1 and $v2 are the same
* @param array $v1 The array of centroids
* @param array $v2 The array of centroids
* @return boolean same or not
*/
function issame($v1, $v2){
	$num = count($v1);
	$dim = count($v1[0]);
	for($n = 0; $n < $num; $n++ ){
		if(isset($v1[$n]) and isset($v2[$n])){
			for($d = 0; $d < $dim; $d++ ){
				if(!isset($v1[$n][$d]) or !isset($v2[$n][$d]) or $v1[$n][$d] != $v2[$n][$d]){
					return false;
				}
			}
		}else{
			return false;
		}
	}
	return true;
}



/**
* Assigns points to one of the centroids 
* @param array $data the data points to cluster
* @param array $centroids The array of centroids
* @param int $k The number of clusters
*/
function assign_points($data, $centroids, $k)
{
	$dim = count($data[0]);
	foreach ($data as $datum_index => $datum)
	{
		foreach ($centroids as $centroid)
		{
			$distances[$datum_index][] = dist($datum, $centroid);
		}
	}
	foreach ($distances as $distance_index => $distance)
	{
		$which_cluster = min_key($distance);
		$tentative_clusters[$which_cluster][] = $distance_index;
		$distances_from_clusters = array("$distance_index" => $distance);
	}
	//in case there's not enough clusters, take the farthest element from any of the cluster's centres
	//and make it a cluster.
	if (count($tentative_clusters) < $k)
	{
		$point_as_cluster = max_key($distances_from_clusters);
		foreach ($tentative_clusters as $tentative_index => $tentative_cluster) 
		{
			foreach ($tentative_cluster as $tentative_element)
			{
				if ($tentative_element == $point_as_cluster)
				{
					$clusters[$k+1][] = $tentative_element;
				}
				else $clusters[$tentative_index][] = $tentative_element;
			}
		}
	}
	else
	{
		$clusters = $tentative_clusters;
	}
	return $clusters;
}



/**
* Creates random starting clusters between the max and min of the data values
* @param $data array An array containing the 
* @param $k int The number of clusters
*/
function randomCentroids($data, $k) {
	$axis = array();
	$dim = count($data[0]);
	foreach ($data as $j)
	{
		for($a = 0; $a < $dim; $a++){
			$axis[$a][] = $j[$a];
		}
	}
	$centroids = array();
	for($kk = 0; $kk < $k; $kk++)
	{
		for($a = 0; $a < $dim; $a++){
                	$centroids[$kk][$a] = rand(min($axis[$a]), max($axis[$a]));
                }
    	}
        return $centroids;
}



/**
* Gets the index of the min value in the array
* @param $array array The array of values to get the max index from
* @return int Index of the min value
*/
function min_key($array) {
	foreach ($array as $k => $val) {
		if ($val == min($array)) return $k;
	}
}



/**
* Gets the index of the max value in the array
* @param $array array The array of values to get the max index from
* @return int Index of the max value
*/
function max_key($array){
	foreach ($array as $k => $val) {
		if ($val == max($array)) return $k;
	}
}

/*
 	USAGE:

		$result = kmeans(array(
			array(1,2,3,4,5),
			array(1,2,3,4,4),
			array(2,3,4,5,6),
			array(1,3,4,5,6),
			array(10,20,30,40,50),
			array(1,5,4,3,2),
			array(1.2,1.4,1.6,1.3,1.5)
		),3);
		echo "CENTROIDS:";
		print_r($result["centroids"]);
		echo "CLUSTERS:";
		print_r($result["clusters"]);
*/
?>