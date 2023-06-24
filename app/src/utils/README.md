# akademiano-utils
[![Build Status](https://travis-ci.org/mrdatamapper/akademiano-utils.svg?branch=master)](https://travis-ci.org/mrdatamapper/akademiano-utils)
[![Coverage Status](https://coveralls.io/repos/github/mrdatamapper/akademiano-utils/badge.svg)](https://coveralls.io/github/mrdatamapper/akademiano-utils)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e5d15682-92e7-46e6-9394-af93fce7637c/mini.png)](https://insight.sensiolabs.com/projects/e5d15682-92e7-46e6-9394-af93fce7637c)
[![Libraries.io ](https://img.shields.io/librariesio/github/mrdatamapper/akademiano-utils.svg)](https://libraries.io/github/mrdatamapper/akademiano-utils)

A library of various functions for the basic operations.

## Installation

composer require akademiano/utils

## Usage
    $array = ["key_1" => ["key_2" => "value_1"]] 
    $var_2 = ArrayTools::get($array, ["key_1", "key_2"]); // $var_2 = "value_1"

## Contributing

1. Fork it!
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request :D

## History

Start as [DeltaUtils](https://github.com/DeltaPHP/DeltaUtils)

## License

[Apache-2.0](https://www.apache.org/licenses/LICENSE-2.0) © mrdatamapper
