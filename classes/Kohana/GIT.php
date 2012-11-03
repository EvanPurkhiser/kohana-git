<?php

class Kohana_GIT {

	/**
	 * Execute a GIT command in the specified working directory
	 *
	 * @param  string $command    The GIT command to execute. This is NOT
	 *                            automagically escaped, be ware!
	 * @param  string $repository The path to the git repository
	 * @return string
	 */
	public static function execute($command, $repository = APPPATH)
	{
		if ( ! `which git`)
			throw new Kohana_Exception("The GIT binary must be installed");

		$command = 'git '.$command;

		// Setup the file descriptors specification
		$descriptsspec = array(
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w'),
		);

		// Store the pipes in this array
		$pipes = array();

		// Execute the command
		$resource = proc_open($command, $descriptsspec, $pipes);

		// Setup the output
		$output = array(
			1 => trim(stream_get_contents($pipes[1])),
			2 => trim(stream_get_contents($pipes[2])),
		);

		// Close the pipes
		array_map('fclose', $pipes);

		// Make sure the process didn't exit with a non-zero value
		if (trim(proc_close($resource)))
			throw new Kohana_Exception($output[2]);

		return $output[1];
	}

}
