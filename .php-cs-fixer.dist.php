<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$fileHeaderComment = <<<COMMENT
This file is part of the polyas-verifier project.

(c) Florian Moser <git@famoser.ch>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
COMMENT;

$finder = PhpCsFixer\Finder::create()
    ->in('src')
    ->in('public');

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@Symfony' => true,
    'array_syntax' => ['syntax' => 'short'],
    'header_comment' => ['header' => $fileHeaderComment, 'separate' => 'both'],
])
    ->setFinder($finder);
