<?php

namespace Peterzaccha\Swaggerator;

use Illuminate\Console\Command;

class Generate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swaggerator:generate {tag} {name} {url} {--method=Get} {--p|prams=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Swagger annotations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tag = $this->argument('tag');
        $name = $this->argument('name');
        $dir = app_path().'/Document';
        $filePath = $dir.'/'.$tag.'.php';

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        if (!file_exists($filePath)) {
             file_put_contents($filePath,'<?php
            ');
        }


        $schema = $this->schemaGenerator($tag,$name,$this->option('method'),$this->argument('url'),$this->option('prams'));

         file_put_contents($filePath,$schema,FILE_APPEND);
         $this->info($tag.' Created');

    }

    private function schemaGenerator($tag,$name,$method,$url,$prames){

        $default = [' *     @OA\Parameter(
 *          name="Content-Type",
 *          description="----",
 *          required=true,
 *          in="header",
 *          @OA\Schema(
 *              type="string",
 *           default="application/json",
 *          )','
 *      ),@OA\Parameter(
 *          name="X-Requested-With",
 *          description="----",
 *          required=true,
 *          in="header",
 *          @OA\Schema(
 *              type="string",
 *           default="XMLHttpRequest",
 *          )
 *      ),'];

        $prames =array_map(function ($pram){
            return '
                 *      @OA\Parameter(
                 *          name="'.$pram.'",
                 *          description="----",
                 *          required=true,
                 *          in="path",
                 *          @OA\Schema(
                 *              type="integer"
                 *          )
                 *      ),
            ';
        },$prames);

        $prames = array_merge($prames,$default);
        return '/**
                 * @OA\\'.$method.'(
                 *      path="/'.$url.'",
                 *      operationId="---",
                 *      tags={"'.$tag.'"},
                 *      summary="----",
                 *      description="---",
                 '.implode('',$prames).'
                 *      @OA\Response(
                 *          response=200,
                 *          description="successful operation"
                 *       ),
                 *      @OA\Response(response=400, description="Bad request"),
                 *      @OA\Response(response=404, description="Resource Not Found"),
                 *      security={
                 *         {
                 *             "oauth2_security_example": {"write:projects", "read:projects"}
                 *         }
                 *     },
                 * )
                 */
                ';


    }
}
