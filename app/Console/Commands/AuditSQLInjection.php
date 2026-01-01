<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AuditSQLInjection extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'security:audit-sql {--fix : Attempt to fix found vulnerabilities}';

    /**
     * The console command description.
     */
    protected $description = 'Audit code for potential SQL injection vulnerabilities';

    private array $dangerousPatterns = [
        '/DB::raw\([^)]*\$/',              // DB::raw with variables
        '/DB::statement\([^)]*\$/',        // DB::statement with variables
        '/->whereRaw\([^)]*\$/',           // whereRaw with variables
        '/->orderByRaw\([^)]*\$/',         // orderByRaw with variables
        '/->selectRaw\([^)]*\$/',          // selectRaw with variables
        '/->havingRaw\([^)]*\$/',          // havingRaw with variables
        '/->groupByRaw\([^)]*\$/',         // groupByRaw with variables
    ];

    private array $safePatterns = [
        '/->where\([^,)]+\s*,\s*[^,)]+\)/', // Parameterized where clauses
        '/->find\([^)]+\)/',                // find() method
        '/->findOrFail\([^)]+\)/',          // findOrFail() method
        '/->whereIn\([^,)]+\s*,\s*\[/',     // whereIn with arrays
    ];

    public function handle(): int
    {
        $files           = File::allFiles(app_path());
        $vulnerabilities = [];
        $safeUsages      = [];

        $this->info('🔍 Scanning for SQL injection vulnerabilities...');

        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        foreach ($files as $file) {
            $content = File::get($file->getPathname());
            $lines   = explode("\n", $content);

            foreach ($lines as $lineNumber => $line) {
                // Check for dangerous patterns
                foreach ($this->dangerousPatterns as $pattern) {
                    if (preg_match($pattern, $line)) {
                        $vulnerabilities[] = [
                            'file'     => $file->getRelativePathname(),
                            'line'     => $lineNumber + 1,
                            'code'     => trim($line),
                            'pattern'  => $pattern,
                            'severity' => 'HIGH',
                        ];
                    }
                }

                // Check for safe patterns (for reporting)
                foreach ($this->safePatterns as $pattern) {
                    if (preg_match($pattern, $line)) {
                        $safeUsages[] = [
                            'file' => $file->getRelativePathname(),
                            'line' => $lineNumber + 1,
                            'code' => trim($line),
                        ];
                    }
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if (empty($vulnerabilities)) {
            $this->info('✅ No potential SQL injection vulnerabilities found!');
            $this->info('📊 Found '.count($safeUsages).' safe parameterized queries');

            return Command::SUCCESS;
        }

        $this->error('⚠️  Found '.count($vulnerabilities).' potential SQL injection vulnerabilities:');
        $this->newLine();

        $highRisk = array_filter($vulnerabilities, fn ($v) => $v['severity'] === 'HIGH');

        if (! empty($highRisk)) {
            $this->error('🚨 HIGH RISK VULNERABILITIES:');
            foreach ($highRisk as $vuln) {
                $this->displayVulnerability($vuln);
            }
            $this->newLine();
        }

        $this->warn('📋 All vulnerabilities:');
        foreach ($vulnerabilities as $vuln) {
            $this->displayVulnerability($vuln);
        }

        $this->newLine();
        $this->info('📊 Found '.count($safeUsages).' safe parameterized queries');

        if ($this->option('fix')) {
            $this->attemptFixes($vulnerabilities);
        }

        return Command::FAILURE;
    }

    private function displayVulnerability(array $vuln): void
    {
        $severityColor = match ($vuln['severity']) {
            'HIGH'   => 'red',
            'MEDIUM' => 'yellow',
            'LOW'    => 'gray',
            default  => 'white',
        };

        $this->line("<fg={$severityColor}>{$vuln['severity']}</> - {$vuln['file']}:{$vuln['line']}");
        $this->line("  <fg=gray>{$vuln['code']}</>");
    }

    private function attemptFixes(array $vulnerabilities): void
    {
        $this->warn('🔧 Attempting automatic fixes...');

        // This would implement automatic fixes for common patterns
        // For now, just show what would be fixed
        foreach ($vulnerabilities as $vuln) {
            $this->line("  Would fix: {$vuln['file']}:{$vuln['line']}");
        }

        $this->info('💡 For automatic fixes, consider using Laravel Pint with custom rules');
    }
}
