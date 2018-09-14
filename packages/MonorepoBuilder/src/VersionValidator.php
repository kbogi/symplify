<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\Composer\Section;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use function Safe\asort;

final class VersionValidator
{
    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    /**
     * @var string[]
     */
    private $sections = [Section::REQUIRE, Section::REQUIRE_DEV];

    public function __construct(JsonFileManager $jsonFileManager)
    {
        $this->jsonFileManager = $jsonFileManager;
    }

    /**
     * @param SplFileInfo[] $fileInfos
     * @return string[][]
     */
    public function findConflictingPackageVersionsInFileInfos(array $fileInfos): array
    {
        $packageVersionsPerFile = [];

        foreach ($fileInfos as $fileInfo) {
            $json = $this->jsonFileManager->loadFromFileInfo($fileInfo);

            foreach ($this->sections as $section) {
                if (! isset($json[$section])) {
                    continue;
                }

                foreach ($json[$section] as $packageName => $packageVersion) {
                    $packageVersionsPerFile[$packageName][$fileInfo->getPathname()] = $packageVersion;
                }
            }
        }

        return $this->filterConflictingPackageVersionsPerFile($packageVersionsPerFile);
    }

    /**
     * @param mixed[] $packageVersionsPerFile
     * @return mixed[]
     */
    private function filterConflictingPackageVersionsPerFile(array $packageVersionsPerFile): array
    {
        $conflictingPackageVersionsPerFile = [];
        foreach ($packageVersionsPerFile as $packageName => $filesToVersions) {
            $uniqueVersions = array_unique($filesToVersions);
            if (count($uniqueVersions) <= 1) {
                continue;
            }

            // sort by versions to make more readable
            asort($filesToVersions);

            $conflictingPackageVersionsPerFile[$packageName] = $filesToVersions;
        }

        return $conflictingPackageVersionsPerFile;
    }
}
