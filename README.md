# Pagination
كلاس تعدد الصفحات 


```php
/**
 * -----------------------------------
 * File  : index.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */
require_once 'Pagination.php';
$rows = [];
for($i = 0 ;$i < 100 ;$i++){
    $rows[$i] = ['name' => 'Mohamed-'.$i,'last' =>'Helal-'.$i];
}
$limit = 10;
$Pagination = \Pagination\Pagination::create(count($rows),$limit,(isset($_GET['page']) ? $_GET['page'] : 1),function ($page){
    return 'index.php?page='.$page;
});


$showing = array_slice($rows,$Pagination->getOffset(),$limit);
foreach ($showing as $item) {
    echo 'Name = '.$item['name'].' | Last = '.$item['last'].'<br/>';
}

echo $Pagination->getPages();
```
