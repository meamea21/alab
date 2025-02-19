<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;
use App\Models\Patient;
use App\Models\Order;
use App\Models\TestResult;
use Illuminate\Support\Facades\Log;

class ImportPatientData extends Command
{
    /**
     * The name and signature of the console command.
     * 
     * php artisan make:command ImportPatientData
     * 
     * php artisan app:import-patient-data
     * php artisan app:import-patient-data storage/private/results.csv
     * 
     * @var string
     */
    protected $signature = 'app:import-patient-data {file}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        echo 'Importing patient data...';
        //

        $file = $this->argument('file');
        
        if (!file_exists($file)) {
            $this->error("File does not exist: $file");
            Log::error("File does not exist:  $file ");
            return 1;
        }

        $csv = Reader::createFromPath($file, 'r');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(';');

        DB::beginTransaction();

        try {
            foreach ($csv as $record) {
                $patient = Patient::firstOrCreate(
                    ['id' => $record['patientId']],
                    [
                        'name' => $record['patientName'],
                        'surname' => $record['patientSurname'],
                        'sex' => $record['patientSex'],
                        'birth_date' => $record['patientBirthDate'],
                    ]
                );

                $order = Order::firstOrCreate(
                    ['id' => $record['orderId']],
                    ['patient_id' => $patient->id]
                );

                TestResult::create([
                    'order_id' => $order->id,
                    'name' => $record['testName'],
                    'value' => $record['testValue'],
                    'reference' => $record['testReference'],
                ]);

                Log::info("Imported record for patient: {$patient->id}");
            }

            DB::commit();
            $this->info('Data imported successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error importing data: " . $e->getMessage());
            $this->error("Error importing data: " . $e->getMessage());
            return 1;
        }




        return 0;

    }
}
