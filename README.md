# configen
Generates apache vhost and bash export from Symfony2 parameters.yml

## Install
```bash
$ wget https://github.com/fnash/configen/releases/download/1.0/configen.phar
```

## Use

```text
 #vhost_template.txt
 
 <VirtualHost *:80>

 {{symfony_vars}}

 </VirtualHost>
```


```bash
$ php configen.pĥar generate parameters.yml vhost --in-template=vhost_template.txt
```
