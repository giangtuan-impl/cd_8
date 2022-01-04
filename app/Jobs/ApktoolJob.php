<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ApktoolJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $apkFolderPath;
    protected $fileName;
    protected $fileNameWithoutExtension;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        $apkFolderPath, 
        $fileName, 
        $fileNameWithoutExtension)
    {
        $this->apkFolderPath = $apkFolderPath;
        $this->fileName = $fileName;
        $this->fileNameWithoutExtension = $fileNameWithoutExtension;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $extractAPKFileCommand = 'cd ' . $this->apkFolderPath . ' && apktool d -f ' . $this->fileName;  // command: cd /path/to/folderHasAPKFile && apktool d -f file.apk
        $addPermissionForExtractedFileCommand = 'chmod -R 777 ' . $this->fileNameWithoutExtension;      // command: chmod -R 777 fileNameWithoutExtension

        $process = new Process($extractAPKFileCommand . ' && ' . $addPermissionForExtractedFileCommand);
        $process->setTimeout(3600);
        $process->run();    //run command

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
