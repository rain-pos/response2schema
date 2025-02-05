<?php

declare(strict_types=1);

namespace DSuurlant\Response2Schema;

use cebe\openapi\exceptions\IOException;
use cebe\openapi\Writer;
use DSuurlant\Response2Schema\Parser\JsonParser;
use Symfony\Component\Yaml\Yaml;

final class Response2Schema
{
    public const VALID_FORMATS = ['yaml', 'json'];

    public static function generate(string $input, string $output): void
    {
        if (file_exists($input) === false) {
            throw SorryCouldNotOpenInputFile::becauseItDoesntExist($input);
        }
        $inputContent = file_get_contents($input);

        try {
            $spec = JsonParser::parse($inputContent);
            if (strrpos($output, 'json', -4)) {
                Writer::writeToJsonFile($spec, $output);
                return;
            }

            if (strrpos($output, 'yaml', -4)) {
                Writer::writeToYamlFile($spec, $output);
                return;
            }
        } catch (IOException $exception) {
            throw SorryCouldNotGenerateOpenApiFile::becauseThereWasAnErrorWritingTheFileToDisk($exception->getMessage());
        }

        throw SorryCouldNotGenerateOpenApiFile::becauseTheOutputFileIsNotInAValidFormat($output);
    }
    
    public static function generateYamlFromJson(string $inputContent, string $output): string
    {
        $spec = JsonParser::parse($inputContent);
        $data = $spec->getSerializableData();
        return Yaml::dump($data, 12, 4, Yaml::DUMP_OBJECT_AS_MAP);
    }
}
