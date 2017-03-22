# SinriDatabaseAgent

![Packagist Version Badge](https://img.shields.io/packagist/v/sinri/sinri-database-agent.svg)
![Packagist](https://img.shields.io/packagist/dt/sinri/sinri-database-agent.svg)
![Packagist](https://img.shields.io/packagist/l/sinri/sinri-database-agent.svg)

Provide a old-style alike quick SQL handler for PHP. 
With this toolkit developers could be free from writing fundamental code for database.

## Composer

    composer require sinri/sinri-database-agent

## Usage

1. Include the `autoload.php` into your PHP project.
2. Declare what you need for convenience.

```PHP
// PDO
use sinri\SinriDatabaseAgent\SinriPDO;
$db=new SinriPDO($params);
// MySQLi
use sinri\SinriDatabaseAgent\SinriMySQLi;
$db=new SinriMySQLi($params);
```

3. General Method

If the following SQL 
`SELECT X,Y FROM T WHERE KEY>0;` 
refers to this records:

<table>
    <tr>
        <td>KEY</td>
        <td>X</td>
        <td>Y</td>
    </tr>
    <tr>
        <td>1</td>
        <td>x1</td>
        <td>y1</td>
    </tr>
    <tr>
        <td>2</td>
        <td>x2</td>
        <td>y2</td>
    </tr>
</table>


```PHP
$sql="SELECT X,Y FROM T WHERE KEY>0;";

$db->getAll($sql);
// return [["X"=>"x1","Y"=>"y1"],["X"=>"x2","Y"=>"y2"]]

$db->getRow($sql);
// return ["X"=>"x1","Y"=>"y1"]

$db->getCol($sql);
// return ["x1","x2"]

$db->getOne($sql);
// return "x1"
```

If you need to do some modification.

```PHP
$sql="INSERT INTO...";
$db->insert($sql);
// return the last inserted record's PK value.

$sql="UPDATE ...";// or DELETE, etc.
$db->exec($sql);
// return affected row count, or FALSE on failure.
```

You may want to use transactions.

```PHP
$db->beginTransaction();

// do some work

$db->inTransaction();
// return if this line is in transaction

if($has_error){
    $db->rollBack();
}else{
    $db->commit();
}
```

## Code Rule: PSR2 

### Check

	phpcs --report=full --standard=PSR2 --ignore=vendor . 
	phpcs --report=summary --standard=PSR2 --ignore=vendor . 

### Correct

	phpcbf --report=full --standard=PSR2 --ignore=vendor .

## License

SinriDatabaseAgent is published under MIT License.