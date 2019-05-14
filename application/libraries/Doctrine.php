<?php
use Doctrine\ORM\Tools\Setup,
	Doctrine\ORM\EntityManager,
	Doctrine\Common\ClassLoader,
    Doctrine\Common\EventManager,
	Doctrine\ORM\Configuration,
    Doctrine\SimpleThings\EntityAudit\AuditConfiguration,
    Doctrine\SimpleThings\EntityAudit\AuditManager;

class Doctrine{

	public $em;

	public function __construct(){	 
        // load database configuration from CodeIgniter
        require APPPATH.'config/database.php';

        // Set up class loading. You could use different autoloaders, provided by your favorite framework,
        // if you want to.
        require_once APPPATH.'libraries/Doctrine/Common/ClassLoader.php';

        $doctrineClassLoader = new ClassLoader('Doctrine',  APPPATH.'libraries');
        $doctrineClassLoader->register();
        $entitiesClassLoader = new ClassLoader('Entity', rtrim(APPPATH. "models" ));
        $entitiesClassLoader->register();
        $proxiesClassLoader = new ClassLoader('Proxies', APPPATH.'models/proxies');
        $proxiesClassLoader->register();

        // Set up caches
        $config = new Configuration;
        
        $cache = new \Doctrine\Common\Cache\ArrayCache;
        #$cache = new \Doctrine\Common\Cache\ApcCache;
        
        $config->setMetadataCacheImpl($cache);
        $driverImpl = $config->newDefaultAnnotationDriver('models/Entity');
        $config->setMetadataDriverImpl($driverImpl);
        $config->setQueryCacheImpl($cache);

        // Proxy configuration
        $config->setProxyDir(APPPATH.'models/Proxies');
        $config->setProxyNamespace('Proxies');

        // Set up logger
        #$logger = new EchoSQLLogger;
        #$config->setSQLLogger($logger);

        $config->setAutoGenerateProxyClasses(TRUE);
		#$config->setAutoGenerateProxyClasses(FALSE);

        // Database connection information      
        $connectionOptions = array(
			'driver'		=> 'pdo_mysql',
			'user'			=> $db['default']['username'],
			'password'		=> $db['default']['password'],
			'host'			=> $db['default']['hostname'],
			'dbname'		=> $db['default']['database'],
			'charset'		=> $db['default']['char_set'],
			'driverOptions'	=> array(
				'charset'	=> $db['default']['char_set'],
			),
		);
        
        // Create EntityManager        
        $this->em = EntityManager::create($connectionOptions, $config);
    }

    private function getUsuarioLogadoCI(){
        #$ci =& get_instance();
        #$ci->dados_usuario_logado = $ci->session->userdata('dados_usuario_logado');
        return isset($_SESSION['dados_user_ci']['login'])?$_SESSION['dados_user_ci']['login']:'';
    }

}