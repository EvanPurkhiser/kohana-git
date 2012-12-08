<?php

class Kohana_GIT {

	/**
	 * Execute a GIT command in the specified working directory
	 *
	 * @param  string $command The GIT command to execute. This is NOT
	 *                         automagically escaped, be ware!
	 * @param  string $cwd     The working directory to run the command under.
	 *                         This can be set to NULL to use the directory the
	 *                         script was run under
	 * @return string
	 */
	public static function execute($command, $cwd = APPPATH)
	{
		if ( ! `which git`)
			throw new Kohana_Exception("The GIT binary must be installed");

		$command = 'git '.$command;

		if ($cwd !== NULL)
		{
			// Change directories to the working tree
			$command = 'cd '.escapeshellarg($cwd).' && '.$command;
		}

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
