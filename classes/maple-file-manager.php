<?php
/**
 * tournamentPreProcess.php
 * 
 * The purpose of this script to create the input file for maple to 
 * calculate the new laws based upon the matched played in a tournament.
 * 
 * A file is created with the filename equal to the tournament id, a
 * system call is then made to add 'tournamentProcess.php' to the `at` 
 * queue. This will then run in turn and will actually update the laws.
 * By default user www-data is on the at.deny list. This user needs to be
 * removed from /etc/at.deny
 * 
 * Current implementation is based on hard coded data, this will need to
 * be changed to be implemented upon submission of a tournament. 
 * 
 */


class MapleFileManager
{
	//directory where all the files will be placed until processed.
	//this directory must also contain tournamentProcess.php
	private $mapleDirectory = "/mapleWorkingDir";
	
	private $matchStrings; //array of strings containing strings describing matches to be written to file.
	
	private $playerStrings; //array of strings of player stats to write to file. 
	private $playerIDs; //array of player ids already added to player strings.
	
	public $tournamentID;
	public $tournamentDate;	//in appropriate stirng format
	public $tournamentType;
	
	public function __construct($eventID, $eventDate, $tournamentType)
	{
		$this->matchStrings = array();
		$this->playerStrings = array();
		$this->playerIDs = array();
		
		$this->tournamentID = $eventID;
		$this->tournamentDate = date_format(date_create($eventDate), 'j/n/Y');
		$this->tournamentType = $tournamentType;
	}

	public function addMatchData($winnerID, $winnerMean, $winnerSD, $winnerLastPlayed, $loserID, $loserMean, $loserSD, $loserLastPlayed, $matchID)
	{
		//add match to string to be written to file
		array_push($this->matchStrings, $matchID . " " . $winnerID . " " . $loserID);
		
		//if winner has not already played add their stats to the file
		if ( ! in_array($winnerID,$this->playerIDs) )
		{
			array_push($this->playerIDs, $winnerID);
			
			$formattedLastPlayed = date_format(date_create($winnerLastPlayed), 'j/n/Y');
			
			array_push($this->playerStrings, $winnerID . " " . $winnerMean . " " . $winnerSD . " " . $formattedLastPlayed);
		}
		
		//if loser has not already played add their stats to the file
		if ( ! in_array($loserID,$this->playerIDs) )
		{
			array_push($this->playerIDs, $loserID);
			
			$formattedLastPlayed = date_format(date_create($loserLastPlayed), 'j/n/Y');
			
			array_push($this->playerStrings, $loserID . " " . $loserMean . " " . $loserSD . " " . $formattedLastPlayed);
		}
	}
	
	public function write()
	{
		$outFile; //output stream to write data
		
		//open file and write pelimary data
		$outFile = fopen($this->mapleDirectory."/".$this->tournamentID,'w');
		fwrite($outFile,$this->tournamentID."\n".$this->tournamentDate."\n");
		
		//write match data
		//count of matches
		fwrite($outFile,count($this->matchStrings) . "\n");
		
		//write match data
		for ($i = 0; $i < count($this->matchStrings); $i++)
		{
			fwrite($outFile, $this->matchStrings[$i] . "\n");
		}
		
		
		//write player data
		//count of players
		fwrite($outFile,count($this->playerStrings) . "\n");
		for ($i = 0; $i < count($this->playerStrings); $i++)
		{
			fwrite($outFile, $this->playerStrings[$i] . "\n");
		}
		
		fclose($outFile);
		
	}
	
	public function addToQueue()
	{
		$working_dir = getcwd();
		chdir($this->mapleDirectory);
		
		if (!strcmp($this->tournamentType, "Single"))
		{
			//singles
			exec("echo \"/bin/php tournamentProcess.php " . $this->tournamentID . "\"" . " | at now");
			#exec("php tournamentProcess.php " . $this->tournamentID);
		}
		else
		{
			//doubles
			exec("echo \"/bin/php tournamentProcessDouble.php " . $this->tournamentID . "\"" . " | at now");
			#exec("php tournamentProcessDouble.php " . $this->tournamentID);
		}

		chdir($working_dir);
	}
	
}
?>
