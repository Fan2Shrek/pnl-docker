<?php

namespace Pnl\PNLDocker\Services\Docker;

use Pnl\PNLDocker\Docker\Container;
use Pnl\PNLDocker\Docker\DockerConfigBag;
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
     * @return array<string, Container>
     */
    public function getContainersFrom(string $path): DockerConfigBag
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

        return new DockerConfigBag($this->readDockerFiles($dockerFiles));
    }

    /**
     * @return array<string, Container>
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

    public function dumpDockerConfig(string $path, DockerConfigBag $dockerConfigBag): void
    {
        $dockerConfig = [
            'version' => '3.9',
            'services' => []
        ];

        foreach ($dockerConfigBag->getContainers() as $container) {
            $ports = array_reduce(
                $container->getPorts(),
                function (array $carry, array $item) {
                    $carry[] = sprintf('%s:%s{{ comment }}', $item['PublicPort'], $item['PrivatePort']);

                    return $carry;
                },
                []
            );

            if (str_starts_with($container->getContainerName(), '/')) {
                $exploded = explode('-', $container->getContainerName());
                $name = $exploded[2];
            } else {
                $name = $container->getContainerName();
            }

            $dockerConfig['services'][$name] = [
                'ports' => $ports,
            ];
        }

        $override = $path . '/docker-compose.override.yml';

        if (!file_exists($override)) {
            touch($override);
        }

        $content = Yaml::dump($dockerConfig, 6, 4, YAML::DUMP_EMPTY_ARRAY_AS_SEQUENCE);
        $content = str_replace("{{ comment }}'", "' #Managed by pnl", $content);
        $content = str_replace("services:", "\nservices:", $content);

        file_put_contents($override, $content);
    }
}
