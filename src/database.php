<?php
namespace App;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class Database {

	public function manager() {
		$paths = [
			__DIR__ . '/entities'
		];
		$devMode = true;
		$config = Setup::createAnnotationMetadataConfiguration($paths, $devMode, null, null, false);

		$conn = [
			'driver' => 'pdo_sqlite',
			'path' => __DIR__ . '/database/db.sqlite'
		];
		$entityManager = EntityManager::create($conn, $config);
		return $entityManager;
	}

}