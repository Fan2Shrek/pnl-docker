<?php

namespace Pnl\PNLDocker\Services;

use Pnl\PNLDocker\Docker\DockerConfig;
use Symfony\Component\Yaml\Yaml;

class DockerContext
{
    public function __construct(
        private DockerConfigFactory $dockerConfigFactory
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

        foreach ($dockerFiles as $dockerFile) {
            $dockerFileContent = Yaml::parseFile($dockerFile);

            if (null === $dockerFileContent || !isset($dockerFileContent['services'])) {
                continue;
            }

            $dockerList = array_reduce(
                $this->dockerConfigFactory->createFromArray($dockerFileContent['services']),
                function ($carry, $item) {
                    $carry[$item->getContainerName()] = $this->dockerConfigFactory->update($item, $carry[$item->getContainerName()] ?? null);

                    return $carry;
                },
                $dockerList
            );
        }

        return $dockerList;
    }
}
