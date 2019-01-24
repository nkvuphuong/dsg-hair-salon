<?php
if ( ! defined( 'IN_ROOT' ) )
{
	print "<h1>Incorrect access </h1>You cannot access this file directly. Do not open this file again, please ! This file is enable protected.";
	exit();
}

// ******************************************************
// ** google api php lient
// ******************************************************
$CMS->api->google_api_analytics = new class_google_api_analytics;
class class_google_api_analytics
{
	public $path_vendor_autoload = ''; // '/google_api_php_client_v2.1.0/vendor/autoload.php';
	
	public $credentials_type = 'service_account'; // service_account or oauth_client

	// used service account credentials
	public $path_service_account_key =  ''; // google_api_php_client_v2.1.0/service-account-ars.json

	// used OAuth client ID credentials
	public $path_oauth_client_key =  ''; // 'google_api_php_client_v2.1.0/client_secret_ars.json';
	public $clientId = ''; // '564146751406-c8acklgelm49inlme85p9kmtiskcrko2.apps.googleusercontent.com';
	public $clientSecret = ''; // '2QEibG1DIZAHinyptD2Nt19r';
	public $redirectUri = ''; // 'http://ars.3f.design/acp/?site=report&act=google_dashboard&subact=oauth2callback';
	public $redirectUriSuccess = ''; // 'http://ars.3f.design/acp/?site=report&act=google_dashboard';
	
	public $vendor_is_load = false;
	public $client = null;
	public $analytics = null;
	public $access_token = null;
	public $response = array();

	public $view_id = '';
	public $queryDateRange = array();
	public $queryMetric = array();
	public $queryDemension = array();
	public $queryOrderBys = array();
	public $queryIncludeEmptyRows = true;

	public function load_config()
	{
		global $CMS;
		$this->path_vendor_autoload = __DIR__ . '/google_api_php_client_v2.1.0/vendor/autoload.php';

		$this->credentials_type = 'service_account';
 
	//$this->path_service_account_key = __DIR__ . '/google_api_php_client_v2.1.0/' . $CMS->vars['ga_path_service_account_key'];

		$this->path_service_account_key = root_path . 'db/googleauth/phuong_service_account_realtorvietnam_org.json';
 

		$this->view_id = "ga:137106467";//$CMS->vars['ga_view_id'];
	}

	public function load_vendor()
	{
		global $CMS;

		// load config
		$this->load_config();

		// Load the Google API PHP Client Library.
		require_once $this->path_vendor_autoload;

		$this->vendor_is_load = true;
	}

	public function load_client_v1()
	{
		global $CS;
 
		// load config
		$this->load_config();

		// Load the Google API PHP Client Library.
		if(!$this->vendor_is_load)
		{
			$this->load_vendor();
		}
 
		// Create and configure a new client object.
		$this->client = new Google_Client();
		$this->client->setApplicationName("Analytics Reporting");
		$this->client->setAuthConfig($this->path_service_account_key);
		$this->client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);

	}
	
	public function load_client()
	{
		global $CMS;

		// load config
		$this->load_config();

		// Load the Google API PHP Client Library.
		if(!$this->vendor_is_load)
		{
			$this->load_vendor();
		}

		// Create and configure a new client object.
		$this->client = new Google_Client();
		// $this->client->setAuthConfig($this->path_oauth_client_key);
		$this->client->setApplicationName('Analytics Reporting');
		$this->client->setClientId($this->clientId);
		$this->client->setClientSecret($this->clientSecret);

		$this->client->getHttpClient()->setDefaultOption('verify', false);//usefor https flag true
		$this->client->setRedirectUri($this->redirectUri);
		$this->client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);
	}

	public function check_valid_access_token()
	{
		global $CMS;

		$valid = false;

		// If the user has already authorized this app then check access_token and get an access token
		// else redirect to ask the user to authorize access to Google Analytics.
		if (isset($_SESSION['access_token']) && $_SESSION['access_token'] && ((time() - $_SESSION['access_token']['created'])<$_SESSION['access_token']['expires_in']))
		{
			$valid = true;
		}

		return $valid;
	}

	public function load_analytics()
	{
		global $CMS;

		// Load the Google API PHP Client Library.
		if(!$this->vendor_is_load)
		{
			//$this->load_vendor();
		}
 
		if($this->credentials_type=='service_account')
		{
			// load client
			if(!$this->client)
			{
				$this->load_client_v1();
			}

			// Create an authorized analytics service object.
		  	$this->analytics = new Google_Service_AnalyticsReporting($this->client);

		}
		else
		{
			// load client
			if(!$this->client)
			{
				$this->load_client();
			}

			// If the user has already authorized this app then check access_token and get an access token
			// else redirect to ask the user to authorize access to Google Analytics.
			// if (isset($_SESSION['access_token']) && $_SESSION['access_token'] && ((time() - $_SESSION['access_token']['created'])<$_SESSION['access_token']['expires_in']))
			if($this->check_valid_access_token())
			{
				// Set the access token on the client.
			  	$this->client->setAccessToken($_SESSION['access_token']);
			  	
			  	// Create an authorized analytics service object.
			  	$this->analytics = new Google_Service_AnalyticsReporting($this->client);
			}
			else
			{
			  	$this->oauth2callback();
			}
		}
	}

	public function oauth2callback()
	{
		global $CMS;

		// load client
		if(!$this->client)
		{
			$this->load_client();
		}

		// Handle authorization flow from the server.
		if (!isset($_GET['code']))
		{
			$auth_url = $this->client->createAuthUrl();

			$CMS->global->redirect($auth_url);
		 	// header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
		}
		else
		{
			$this->client->authenticate($_GET['code']);
			$this->access_token = $this->client->getAccessToken();
			$_SESSION['access_token'] = $this->access_token;

			$CMS->global->redirect($this->redirectUriSuccess);
			// header('Location: ' . filter_var($this->redirectUri, FILTER_SANITIZE_URL));
		}
	}

	public function setqueryReset()
	{
		global $CMS;

		$this->queryDateRange = array();
		$this->queryMetric = array();
		$this->queryDemension = array();
		$this->queryOrderBys = array();
		$this->queryIncludeEmptyRows = true;
	}

	public function setqueryDateRanges($startDate, $endDate)
	{
		global $CMS;

		// Load the Google API PHP Client Library.
		if(!$this->vendor_is_load)
		{
			$this->load_vendor();
		}

		$startDate = (!empty($startDate) && !is_array($startDate)) ? $startDate : 'today';
		$endDate = (!empty($endDate) && !is_array($endDate))? $endDate : 'today';

		// Create the DateRange object.
	  	$dateRange = new Google_Service_AnalyticsReporting_DateRange();
	  	$dateRange->setStartDate($startDate);
	  	$dateRange->setEndDate($endDate);

	  	// set
		$this->queryDateRange[] = $dateRange;
	}

	public function setqueryMetric($expression, $alias)
	{
		global $CMS;

		// Load the Google API PHP Client Library.
		if(!$this->vendor_is_load)
		{
			$this->load_vendor();
		}

		$expression = (!empty($expression) && !is_array($expression)) ? $expression : 'ga:sessions';
		$alias = (!empty($alias) && !is_array($alias))? $alias : $expression;

		// Create the Metrics object.
		$metric = new Google_Service_AnalyticsReporting_Metric();
		$metric->setExpression($expression);
		$metric->setAlias($alias);

		// set
		$this->queryMetric[] = $metric;
	}

	public function setqueryDimension($name)
	{
		global $CMS;

		// Load the Google API PHP Client Library.
		if(!$this->vendor_is_load)
		{
			$this->load_vendor();
		}

		$name = (!empty($name) && !is_array($name)) ? $name : 'ga:date';

		// Create the segment dimension.
		$dimension = new Google_Service_AnalyticsReporting_Dimension();
		$dimension->setName($name);

		// set
		$this->queryDemension[] = $dimension;
	}

	public function setqueryOrderBys($sortname, $sortorder, $sorttype)
	{
		global $CMS;

		// Load the Google API PHP Client Library.
		if(!$this->vendor_is_load)
		{
			$this->load_vendor();
		}

		$sortname = (!empty($sortname) && !is_array($sortname))? $sortname : 'ga:date';
		$sortorder = (!empty($sortorder) && !is_array($sortorder))? $sortorder : 'ASCENDING'; // DESCENDING
		$sorttype = (!empty($sorttype) && !is_array($sorttype))? $sorttype : 'VALUE';

		// Create the segment dimension.
		$ordering = new Google_Service_AnalyticsReporting_OrderBy();
		$ordering->setFieldName("{$sortname}");
		$ordering->setOrderType("{$sorttype}");
		$ordering->setSortOrder("{$sortorder}");

		// set
		$this->queryOrderBys[] = $ordering;
	}

	public function setqueryIncludeEmptyRows($includeemptyrows)
	{
		global $CMS;

		$includeemptyrows = $includeemptyrows? true : false;
		$this->queryIncludeEmptyRows = $includeemptyrows;
	}

	public function getReport($includeemptyrows=true)
	{
		global $CMS;

		// Load the Google API PHP Client Library.
		if(!$this->vendor_is_load)
		{
			//$this->load_vendor();
		}

 
	    $this->load_client_v1();
			 
		if(!$this->analytics)
		{
			$this->load_analytics();
		}

	  	// Replace with your view ID. E.g., XXXX.
	  	//$VIEW_ID = "ga:137474845";
 
		// Create the ReportRequest object.
		$request = new Google_Service_AnalyticsReporting_ReportRequest();
		$request->setViewId($this->view_id);
	 
		if(!empty($this->queryDateRange))
		{
			$request->setDateRanges($this->queryDateRange);
		}

		if(!empty($this->queryMetric))
		{
			$request->setMetrics($this->queryMetric);
		}
	  	
	  	if(!empty($this->queryDemension))
	  	{
	  		$request->setDimensions($this->queryDemension);
	  	}
 
	  	if(!empty($this->queryOrderBys))
	  	{
	  		$request->setOrderBys($this->queryOrderBys);
	  	}

	  	$request->setIncludeEmptyRows($this->queryIncludeEmptyRows);

	  	$body = new Google_Service_AnalyticsReporting_GetReportsRequest();
	  	$body->setReportRequests(array($request));
	 
	  	$this->response = $this->analytics->reports->batchGet($body);
	  
	  	return $this->response;
	}
	
	public function printResults($reports)
	{
	  for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ )
	  {
	    $report = $reports[ $reportIndex ];
	    $header = $report->getColumnHeader();
	    $dimensionHeaders = $header->getDimensions();
	    $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
	    $rows = $report->getData()->getRows();
		
	    for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++)
	    {
	      $row = $rows[ $rowIndex ];
	      $dimensions = $row->getDimensions();
	      $metrics = $row->getMetrics();
		  
	      for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++)
	      {
	        print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "\n");
	      }

	      for ($j = 0; $j < count( $metricHeaders ) && $j < count( $metrics ); $j++)
	      {
	        $entry = $metricHeaders[$j];
	        $values = $metrics[$j];
	        print("Metric type: " . $entry->getType() . "\n" );
	        for ( $valueIndex = 0; $valueIndex < count( $values->getValues() ); $valueIndex++ )
	        {
	          $value = $values->getValues()[ $valueIndex ];
	          print($entry->getName() . ": " . $value . "\n");
	        }
	      }
	    }
	  }
	}
}

?>