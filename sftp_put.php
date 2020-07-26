#!/usr/bin/php
<?php
  /*
          File: sftp_put.php
       Created: 07/23/2020
       Updated: 07/23/2020
    Programmer: Cuates
    Updated By: Cuates
       Purpose: Create comma delimited CSV files with database data and ZIP the files to put onto an STP server
  */

  // Include error check class
  include ("checkerrorclass.php");

  // Create an object of error check class
  $checkerrorcl = new checkerrorclass();

  // Set variables
  $developerNotify = 'cuates@email.com'; // Production email(s)
  // $developerNotify = 'cuates@email.com'; // Development email(s)
  $endUserEmailNotify = 'cuates@email.com'; // Production email(s)
  // $endUserEmailNotify = 'cuates@email.com'; // Development email(s)
  $externalEndUserEmailNotify = ''; // Production email(s)
  // $externalEndUserEmailNotify = 'cuates@email.com'; // Development email(s)
  $scriptName = 'SFTP Put'; // Production
  // $scriptName = 'TEST SFTP Put TEST'; // Development
  $fromEmailServer = 'Email Server';
  $fromEmailNotifier = 'email@email.com';

  // Retrieve any other issues not retrieved by the set_error_handler try/catch
  // Parameters are function name, $email_to, $email_subject, $from_mail, $from_name, $replyto, $email_cc and $email_bcc
  register_shutdown_function(array($checkerrorcl,'shutdown_notify'), $developerNotify, $scriptName . ' Error', $fromEmailNotifier, $fromEmailServer, $fromEmailNotifier);

  // Function to catch exception errors
  set_error_handler(function ($errno, $errstr, $errfile, $errline)
  {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
  });

  // Attempt script logic
  try
  {
    // // Set new memory limit Note: This will revert back to original limit upon end of script
    // ini_set('memory_limit', '4095M');

    // Declare download directory
    define ('TEMPDOC', '/var/www/html/Temp_Directory/');
    define ('GENERATEDDOC', '/var/www/html/Doc_Directory/');

    // Set local
    // setlocale(LC_ALL, "en_US.utf8");

    // Include class file
    include ("sftp_put_class.php");

    // Create an object of class
    $sftp_put_cl = new sftp_put_class();

    // Initialize variables
    $errorPrefixFilename = "sftp_put_issue_"; // Production
    // $errorPrefixFilename = "sftp_put_dev_issue_"; // Development
    $errormessagearray = array();
    $idNum = 0;
    $timestampNumber = date("YmdHis");
    $zipFileName = "File_Name_" . $timestampNumber . "_" . date("YmdHis") . '.zip';
    $file01 = "file01.csv";
    $file02 = "file02.csv";
    $file03 = "file03.csv";
    $file04 = "file04.csv";
    $file05 = "file05.csv";
    $file06 = "file06.csv";
    $path = GENERATEDDOC . "Put/";
    $archivePath = $path . "Archive/";
    $sftpPutFile = 0;
    $totalSftpPutFile = 6;

    // Set information
    $dataInformation = array(array("OptionMode" => "file01", "Filename" => $file01, "Header" => array('Column01', 'Column02')), array("OptionMode" => "file02", "Filename" => $file02, "Header" => array('Column01', 'Column02')), array("OptionMode" => "file03", "Filename" => $file03, "Header" => array('Column01', 'Column02')), array("OptionMode" => "file04", "Filename" => $file04, "Header" => array('Column01', 'Column02')), array("OptionMode" => "file05", "Filename" => $file05, "Header" => array('Column01', 'Column02')), array("OptionMode" => "file06", "Filename" => $file06, "Header" => array('Column01', 'Column02')));

    // Perform data retrieval for all in the array
    foreach($dataInformation as $infoVal)
    {
      // Set parameters
      $optionMode = reset($infoVal);
      $filename = next($infoVal);
      $headerColumns = next($infoVal);

      // Initialize variables and arrays
      $dataValue = array();

      // Retrieve data
      $dataValue = $sftp_put_cl->extractData($optionMode, $timestampNumber, $headerColumns);

      // Check if server error
      if (!isset($dataValue['SError']) && !array_key_exists('SError', $dataValue))
      {
        // Check if data value is given
        if (count($dataValue) > 0)
        {
          // Initialize column headers
          $colHeaders = array();

          // Write to file for later processing
          $createFileNameResult = $sftp_put_cl->writeToFile($path, $filename, $dataValue, $colHeaders);

          // Explode database message
          $createFileNameReturn = explode('~', $createFileNameResult);

          // Set response message
          $createFileNameResp = reset($createFileNameReturn);
          $createFileNameMesg = next($createFileNameReturn);

          // Check if success with writing the files
          if (trim($createFileNameResp) === "Success")
          {
            // Increment flag to put into SFTP servers
            $sftpPutFile++;
          }
          else
          {
            // Else there was an error writing to file
            // Append error message
            array_push($errormessagearray, array('Write Data to File', $optionMode, $path, $archivePath, $filename, implode(', ', $headerColumns), $timestampNumber, $zipFileName, '', 'Error', $createFileNameMesg));
          }
        }
      }
      else
      {
        // Set response and message
        $dataValueMesg = reset($dataValue);

        // Append error message
        array_push($errormessagearray, array('Data Extract', $optionMode, $path, $archivePath, $filename, implode(', ', $headerColumns), $timestampNumber, $zipFileName, '', 'Error', $dataValueMesg));
      }
    }

    // Check if not all files are present
    // If not present delete the files as the script will not need to send the file via SFTP
    if ($sftpPutFile !== $totalSftpPutFile)
    {
      // Check if file exist
      if (file_exists($path . $file01))
      {
        // Delete the unwanted file from the server
        unlink($path . $file01);
      }

      // Check if file exist
      if (file_exists($path . $file02))
      {
        // Delete the unwanted file from the server
        unlink($path . $file02);
      }

      // Check if file exist
      if (file_exists($path . $file03))
      {
        // Delete the unwanted file from the server
        unlink($path . $file03);
      }

      // Check if file exist
      if (file_exists($path . $file04))
      {
        // Delete the unwanted file from the server
        unlink($path . $file04);
      }

      // Check if file exist
      if (file_exists($path . $file05))
      {
        // Delete the unwanted file from the server
        unlink($path . $file05);
      }

      // Check if file exist
      if (file_exists($path . $file06))
      {
        // Delete the unwanted file from the server
        unlink($path . $file06);
      }
    }

    // Check if error message array is not empty
    if (count($errormessagearray) <= 0 && $sftpPutFile === $totalSftpPutFile)
    {
      // Change directory to be able to zip files
      chdir($path);

      // Execute a Linux command for zipping files
      shell_exec("zip " . $path . $zipFileName . " " . $file01 . " " . $file02 . " " . $file03 . " " . $file04 . " " . $file05 . " " . $file06);

      // Check if file exist
      if (file_exists($path . $zipFileName) && trim($zipFileName) !== "")
      {
        // Put file on to the remote server
        $fileTransfer = $sftp_put_cl->putSFTPFile($zipFileName, 'SFTP', $path);

        // Explode database message
        $fileTransferReturn = explode('~', $fileTransfer);

        // Set response message
        $fileTransferResp = reset($fileTransferReturn);
        $fileTransferMesg = next($fileTransferReturn);

        // Check if a file was transferred successfully
        if (trim($fileTransferResp) === "Success")
        {
          // Check if file exist
          if (file_exists($path . $zipFileName))
          {
            // Delete the unwanted file from the server
            unlink($path . $zipFileName);
          }

          // Check if file exist
          if (file_exists($path . $file01))
          {
            // Delete the unwanted file from the server
            unlink($path . $file01);
          }

          // Check if file exist
          if (file_exists($path . $file02))
          {
            // Delete the unwanted file from the server
            unlink($path . $file02);
          }

          // Check if file exist
          if (file_exists($path . $file03))
          {
            // Delete the unwanted file from the server
            unlink($path . $file03);
          }

          // Check if file exist
          if (file_exists($path . $file04))
          {
            // Delete the unwanted file from the server
            unlink($path . $file04);
          }

          // Check if file exist
          if (file_exists($path . $file05))
          {
            // Delete the unwanted file from the server
            unlink($path . $file05);
          }

          // Check if file exist
          if (file_exists($path . $file06))
          {
            // Delete the unwanted file from the server
            unlink($path . $file06);
          }
        }
        else
        {
          // Append error message
          array_push($errormessagearray, array('Put File on Server', '', $path, '', '', '', $timestampNumber, $zipFileName, '', 'Error', $fileTransferMesg));
        }
      }
      else
      {
        // Append error message
        array_push($errormessagearray, array('ZIP Issue', '', $path, '', '', '', $timestampNumber, $zipFileName, '', 'Error', 'There was an issue zipping the files'));
      }
    }

    // Check if error message array is not empty
    if (count($errormessagearray) <= 0)
    {
      // Update the sequence invoice in the database
      $sequenceUpdate = $sftp_put_cl->updateSequence($idNum);

      // Explode database message
      $sequenceUpdateData = explode('~', $sequenceUpdate);

      // Set response message
      $sequenceUpdateResp = reset($sequenceUpdateData);
      $sequenceUpdateMesg = next($sequenceUpdateData);

      // Check if error with registering process
      if (trim($sequenceUpdateResp) !== "Success")
      {
        // Append error message
        array_push($errormessagearray, array('Update Sequence', '', '', '', '', '', $timestampNumber, $zipFileName, $sequenceNumber, 'Error', $sequenceUpdateMesg));
      }
    }

    // Check if error message array is not empty
    if (count($errormessagearray) > 0)
    {
      // Check if file exist
      if (file_exists($path . $zipFileName))
      {
        // Delete the unwanted file from the server
        unlink($path . $zipFileName);
      }

      // Check if file exist
      if (file_exists($path . $file01))
      {
        // Delete the unwanted file from the server
        unlink($path . $file01);
      }

      // Check if file exist
      if (file_exists($path . $file02))
      {
        // Delete the unwanted file from the server
        unlink($path . $file02);
      }

      // Check if file exist
      if (file_exists($path . $file03))
      {
        // Delete the unwanted file from the server
        unlink($path . $file03);
      }

      // Check if file exist
      if (file_exists($path . $file04))
      {
        // Delete the unwanted file from the server
        unlink($path . $file04);
      }

      // Check if file exist
      if (file_exists($path . $file05))
      {
        // Delete the unwanted file from the server
        unlink($path . $file05);
      }

      // Check if file exist
      if (file_exists($path . $file06))
      {
        // Delete the unwanted file from the server
        unlink($path . $file06);
      }

      // Set prefix file name and headers
      $errorFilename = $errorPrefixFilename . date("Y-m-d_H-i-s") . '.csv';
      $colHeaderArray = array(array('Process', 'Option Mode', 'Path', 'Archive', 'File Name', 'Header', 'Time Stamp Number', 'ZIP File Name', 'Sequence Number', 'Response', 'Message'));

      // Initialize variable
      $to = "";
      $to = $developerNotify;
      $to_cc = "";
      $to_bcc = "";
      $fromEmail = $fromEmailNotifier;
      $fromName = $fromEmailServer;
      $replyTo = $fromEmailNotifier;
      $subject = $scriptName . " Error";

      // Set the email headers
      $headers = "From: " . $fromName . " <" . $fromEmail . ">" . "\r\n";
      // $headers .= "CC: " . $to_cc . "\r\n";
      // $headers .= "BCC: " . $to_bcc . "\r\n";
      $headers .= "MIME-Version: 1.0\r\n";
      $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
      // $headers .= "X-Priority: 3\r\n";

      // Mail priority levels
      // "X-Priority" (values: 1, 3, or 5 from highest[1], normal[3], lowest[5])
      // Set priority and importance levels
      $xPriority = "";

      // Set the email body message
      $message = "<!DOCtype html>
      <html>
        <head>
          <title>"
            . $scriptName .
            " Error
          </title>
          <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
          <!-- Include next line to use the latest version of IE -->
          <meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge\" />
        </head>
        <body>
          <div style=\"text-align: center;\">
            <h2>"
              . $scriptName .
              " Error
            </h2>
          </div>
          <div style=\"text-align: center;\">
            There was an issue with " . $scriptName . " Error process.
            <br />
            <br />
            Do not reply, your intended recipient will not receive the message.
          </div>
        </body>
      </html>";

      // Call notify developer function
      $sftp_put_cl->notifyDeveloper(TEMPDOC, $errorFilename, $colHeaderArray, $errormessagearray, $to, $to_cc, $to_bcc, $fromEmail, $fromName, $replyTo, $subject, $headers, $message, $xPriority);
    }
  }
  catch(Exception $e)
  {
    // Call to the function
    $checkerrorcl->caught_error_notify($e, $developerNotify, $scriptName . ' Error', $fromEmailNotifier, $fromEmailServer, $fromEmailNotifier);
  }
?>