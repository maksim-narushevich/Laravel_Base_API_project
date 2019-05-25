<?php

use App\Helpers\DatabaseConnection;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Testwork\Hook\Scope\AfterSuiteScope;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Imbo\BehatApiExtension\Context\ApiContext;
use Psr\Http\Message\RequestInterface;
use SebastianBergmann\CodeCoverage;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Behat\Behat\Tester\Exception\PendingException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Illuminate\Contracts\Console\Kernel;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends ApiContext implements Context, SnippetAcceptingContext
{


    //    use \Behat\Symfony2Extension\Context\KernelDictionary;
//
//    private static $codeCoverageSessionId;
//    private static $codeCoverageCollectionUri;

    /**
     * @var string
     */
    private $token;

//    /**
//     * @var Response|null
//     */
//    protected $response;
//
//    private static $behatCoverageReportPath = __DIR__ . "/../../logs/behat/coverage/behat.coverage.xml";
//
//    /**
//     * @BeforeSuite
//     */
//    public static function setUp(BeforeSuiteScope $scope) {
//        if (!$scope->getSuite()->getSettings()['codeCoverage']['enabled']) {
//            return;
//        }
//        self::$codeCoverageSessionId = uniqid('', true);
//    }
//    /**
//     * @AfterSuite
//     */
//    public static function tearDown(AfterSuiteScope $scope) {
//        $suite = $scope->getSuite();
//        $settings = $suite->getSettings()['codeCoverage'];
//        if (!$settings['enabled'] || empty(self::$codeCoverageCollectionUri)) {
//            return;
//        }
//        $context = stream_context_create([
//            'http' => [
//                'header' => implode("\r\n", [
//                    sprintf('X-Code-Coverage-Session-Id: %s', self::$codeCoverageSessionId),
//                    'X-Collect-Code-Coverage: 1',
//                ]),
//            ]
//        ]);
//
//        $response = file_get_contents(self::$codeCoverageCollectionUri, false, $context);
//        $data = unserialize($response);
//        $filter = new CodeCoverage\Filter();
//
//        foreach ($settings['whitelist']['directories'] as $dir) {
//            $filter->addDirectoryToWhitelist($dir);
//        }
//        $coverage = new CodeCoverage\CodeCoverage(null, $filter);
//        $coverage->append($data, 'behat-suite');
//        $report = new CodeCoverage\Report\Html\Facade();
//        $report->process($coverage, sprintf('%s/%s', $settings['output'], $suite->getName()));
//
//        //-- Generate XML report
//        $reportXML = new \SebastianBergmann\CodeCoverage\Report\Clover;
//        $reportXML->process($coverage, self::$behatCoverageReportPath);
//    }
//
//    /**
//     * SEt API client for REST API code coverage
//     */
//    public function setClient(ClientInterface $client) {
//        $stack = $client->getConfig('handler');
//        if (!empty(self::$codeCoverageSessionId)) {
//            self::$codeCoverageCollectionUri = (string) $client->getConfig()['base_uri'].":80";
//            $stack->push(Middleware::mapRequest(function(RequestInterface $request) {
//                return $request
//                    ->withHeader('X-Code-Coverage-Session-Id', self::$codeCoverageSessionId)
//                    ->withHeader('X-Enable-Code-Coverage', 1);
//            }));
//        }
//        return parent::setClient($client);
//    }

    /**
     * @Then /^save response token$/
     */
    public function saveResponseToken()
    {
        $this->token = $this->getResponseBody()->success->token;
    }

    /**
     * Set authorization token in header
     *
     * @return self
     *
     * @Given set authorization token
     */
    public function setJWTTokenINHeader()
    {
        $header = "Authorization";
        $value = "Bearer " . $this->token;
        $this->request = $this->request->withHeader($header, $value);

        return $this;
    }

    /**
     * Request a path
     *
     * @When I delete user with email :email with request to :path using HTTP :method
     *
     * @param $email
     * @param $path
     * @param $method
     *
     * @return \FeatureContext
     */
    public function iDeleteUserWithEmailWithRequestToUsingHTTPDELETE($email, $path, $method)
    {
        $user = User::where('email', $email)->firstOrFail();
        if ($user === null) {
            throw new \RuntimeException('User not found');
        }

        $this->setRequestPath($path);
        $this->request = $this->request->withMethod($method);
        $this->sendRequest();
        return $this;
    }

    /**
     * @Given /^purge DB$/
     */
    public function purgeDB()
    {
        $tableNames = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($tableNames as $name) {
            // Don't truncate 'migrations' table
            if ($name == 'migrations') {
                continue;
            }
            DB::table($name)->delete();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

    /**
     * @Given /^generate secure access Passport JWT tokens$/
     */
    public function generateSecureAccessPassportJWTTokens()
    {
        Artisan::call('passport:install');
    }

    /**
     * @Given /^run database migrations$/
     */
    public function runDatabaseMigrations()
    {
        Artisan::call('migrate');
    }


    /**
     * @Given /^setup environment from "([^"]*)" file with "([^"]*)" env$/
     * @param $file
     * @param $env
     */
    public function setupEnvironmentFromFileWithEnv($file, $env)
    {
        if (Storage::disk('root')->exists('./' . $file)) {

            if (Storage::disk('root')->exists('./.env')&& $env!=='restore_behat') {
                //-- Set temporary environment file & remove current one
                Storage::disk('root')->move('.env', './.env.temp');
            }else if ($env!=='restore_behat'){
                Storage::disk('root')->copy('.env.dist', './.env.temp');
            }

            $this->deleteTemporaryFileIfExist('.env');
            switch ($env) {
                case "test":
                    Storage::disk('root')->copy('./' . $file, '.env');
                    break;
                case "restore_behat":
                    Storage::disk('root')->copy('./.env.temp', '.env');
                    break;
                default:
                    Storage::disk('root')->copy('./env.dist', '.env');
            }
        } else {
            throw new PendingException($file . " does NOT exist");
        }
    }

    /**
     * @Given /^delete temporary "([^"]*)" file if exist$/
     * @param $file
     */
    public function deleteTemporaryFileIfExist($file)
    {
        if (Storage::disk('root')->exists($file)) {
            //-- Set temporary environment file & remove current one
            Storage::disk('root')->delete($file);
        }
    }


}