##abarth500 / K-Means-PHP
n-dimensional k-Means function for PHP.

https://github.com/abarth500/K-Means-PHP


##Usage
<pre><code>
include_once(kmeans.php);

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
</code></pre>