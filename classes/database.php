<?php

class Database
{
	private $connection;

	public function __construct()
	{
		$configuration = parse_ini_file("./configurations/config.ini");

		try
		{
			$dsn = "mysql:host=".$configuration["hostname"].";dbname=".$configuration["databaseName"].";charset=".$configuration["charset"];

			$this->connection = new PDO($dsn, $configuration["username"], $configuration["password"]);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $exception)
		{
			echo "Database connection failed. Contact the server administrator";
		}
	}
	
	public function fixLimitProblem($set)
	{
		$this->connection->setAttribute( PDO::ATTR_EMULATE_PREPARES , $set);
	}

	public function query($query, $parameters = NULL)
	{
		try
		{
			$result = $this->connection->prepare($query);
			$result->execute($parameters);
		}
		catch(PDOException $exception)
		{
			
		}

		return $result;
	}
}	

?>
