<?php

namespace PackageVersions;

/**
 * This class is generated by ocramius/package-versions, specifically by
 * @see \PackageVersions\Installer
 *
 * This file is overwritten at every run of `composer install` or `composer update`.
 */
final class Versions
{
    const ROOT_PACKAGE_NAME = '__root__';
    const VERSIONS = array (
  'composer/xdebug-handler' => '1.3.0@b8e9745fb9b06ea6664d8872c4505fb16df4611c',
  'jean85/pretty-package-versions' => '1.2@75c7effcf3f77501d0e0caa75111aff4daa0dd48',
  'mongodb/mongodb' => '1.4.2@bd148eab0493e38354e45e2cd7db59b90fdcad79',
  'monolog/monolog' => '1.23.0@fd8c787753b3a2ad11bc60c063cff1358a32a3b4',
  'nette/bootstrap' => 'v2.4.6@268816e3f1bb7426c3a4ceec2bd38a036b532543',
  'nette/di' => 'v2.4.14@923da3e2c0aa53162ef455472c0ac7787b096c5a',
  'nette/finder' => 'v2.4.2@ee951a656cb8ac622e5dd33474a01fd2470505a0',
  'nette/neon' => 'v2.4.3@5e72b1dd3e2d34f0863c5561139a19df6a1ef398',
  'nette/php-generator' => 'v3.0.5@ea90209c2e8a7cd087b2742ca553c047a8df5eff',
  'nette/robot-loader' => 'v3.1.0@fc76c70e740b10f091e502b2e393d0be912f38d4',
  'nette/utils' => 'v2.5.3@17b9f76f2abd0c943adfb556e56f2165460b15ce',
  'nikic/php-parser' => 'v4.0.4@fa6ee28600d21d49b2b4e1006b48426cec8e579c',
  'ocramius/package-versions' => '1.3.0@4489d5002c49d55576fa0ba786f42dbb009be46f',
  'phpstan/phpdoc-parser' => '0.3@ed3223362174b8067729930439e139794e9e514a',
  'phpstan/phpstan' => '0.10.3@dc62f78c9aa6e9f7c44e8d6518f1123cd1e1b1c0',
  'psr/log' => '1.0.2@4ebe3a8bf773a19edfe0a84b6585ba3d401b724d',
  'symfony/console' => 'v4.1.4@ca80b8ced97cf07390078b29773dc384c39eee1f',
  'symfony/finder' => 'v4.1.4@e162f1df3102d0b7472805a5a9d5db9fcf0a8068',
  'symfony/polyfill-mbstring' => 'v1.9.0@d0cd638f4634c16d8df4508e847f14e9e43168b8',
  '__root__' => 'No version set (parsed as 1.0.0)@',
);

    private function __construct()
    {
    }

    /**
     * @throws \OutOfBoundsException if a version cannot be located
     */
    public static function getVersion(string $packageName) : string
    {
        if (isset(self::VERSIONS[$packageName])) {
            return self::VERSIONS[$packageName];
        }

        throw new \OutOfBoundsException(
            'Required package "' . $packageName . '" is not installed: cannot detect its version'
        );
    }
}
