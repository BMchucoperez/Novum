<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckUploadConfig extends Command
{
    protected $signature = 'upload:check';
    protected $description = 'Check and display current upload configuration';

    public function handle()
    {
        $this->info('Current PHP Upload Configuration:');
        $this->line('upload_max_filesize: ' . ini_get('upload_max_filesize'));
        $this->line('post_max_size: ' . ini_get('post_max_size'));
        $this->line('max_execution_time: ' . ini_get('max_execution_time'));
        $this->line('memory_limit: ' . ini_get('memory_limit'));
        $this->line('max_file_uploads: ' . ini_get('max_file_uploads'));
        $this->line('max_input_time: ' . ini_get('max_input_time'));
        
        // Convertir a bytes para comparación
        $uploadMax = $this->convertToBytes(ini_get('upload_max_filesize'));
        $postMax = $this->convertToBytes(ini_get('post_max_size'));
        
        $this->line('');
        if ($uploadMax >= 10485760) { // 10MB en bytes
            $this->info('✓ upload_max_filesize is sufficient for 10MB files');
        } else {
            $this->error('✗ upload_max_filesize is too small. Need at least 10M');
        }
        
        if ($postMax >= 12582912) { // 12MB en bytes
            $this->info('✓ post_max_size is sufficient');
        } else {
            $this->error('✗ post_max_size is too small. Need at least 12M');
        }
        
        return 0;
    }
    
    private function convertToBytes($value)
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value)-1]);
        $value = (int) $value;
        
        switch($last) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }
        
        return $value;
    }
}