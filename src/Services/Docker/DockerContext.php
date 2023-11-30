<?php

namespace Pnl\PNLDocker\Services\Docker;

use Pnl\PNLDocker\Docker\DockerConfig;
use Pnl\PNLDocker\Services\Docker\Factory\DockerFileConfigFactory;
use Symfony\Component\Yaml\Yaml;

/**
 * Create a docker context from a docker-compose file
 */
class DockerContext
{
    public function __construct(
        private DockerFileConfigFactory $dockerFileConfigFactory
    ) {
    }

    /**
     * @return array<string, DockerConfig>
     */
    public function getContainersFrom(string $path): array
    {
        $dockerFiles = [];
        foreach (new \DirectoryIterator($path) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            if ($fileInfo->isDir()) {
                continue;
            }

            if (!str_contains($fileInfo->getFilename(), 'docker-compose')) {
                continue;
            }

            $dockerFiles[] = $fileInfo->getPathname();
        }

        return $this->readDockerFiles($dockerFiles);
    }

    /**
     * @return array<string, DockerConfig>
     */
    private function readDockerFiles(array $dockerFiles): array
    {
        $dockerList = [];
        rsort($dockerFiles);

        foreach ($dockerFiles as $dockerFile) {
            $dockerFileContent = Yaml::parseFile($dockerFile);

            if (null === $dockerFileContent || !isset($dockerFileContent['services'])) {
                continue;
            }

            foreach ($dockerFileContent['services'] as $containerName => $container) {
                if (!isset($dockerList[$containerName])) {
                    $dockerList[$containerName] = [
                        'name' => $containerName,
                        'image' => $container['image'],
                        'ports' => $container['ports'] ?? []
                    ];
                } else {
                    foreach ($container as $key => $value) {
                        $dockerList[$containerName][$key] = array_merge($value, $dockerList[$containerName][$key]);
                    }
                }
            }
        }

        return $this->dockerFileConfigFactory->createFromArray($dockerList);
    }
}
