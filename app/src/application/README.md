# akademiano-application
[![Build Status](https://travis-ci.org/mrdatamapper/akademiano-application.svg?branch=master)](https://travis-ci.org/mrdatamapper/akademiano-application)
[![Coverage Status](https://coveralls.io/repos/github/mrdatamapper/akademiano-application/badge.svg)](https://coveralls.io/github/mrdatamapper/akademiano-application)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/84e94028-f979-46c7-bef3-c82f85d732bd/mini.png)](https://insight.sensiolabs.com/projects/84e94028-f979-46c7-bef3-c82f85d732bd)
[![Libraries.io ](https://img.shields.io/librariesio/github/mrdatamapper/akademiano-application.svg)](https://libraries.io/github/mrdatamapper/akademiano-application)
[![GitHub release](https://img.shields.io/github/release/mrdatamapper/akademiano-application.svg)]()

Akademiano skeleton project

## .htaccess example
```
<IfModule mod_rewrite.c>
  RewriteEngine on
  RewriteBase /
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-l
  RewriteCond %{REQUEST_URI} !=/favicon.ico
  RewriteRule ^(.*)$ index.php [L]
</IfModule>
```

## Contributing

1. Fork it!
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request :D

## License

[Apache-2.0](https://www.apache.org/licenses/LICENSE-2.0) © mrdatamapper
