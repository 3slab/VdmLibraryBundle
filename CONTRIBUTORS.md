# How to test

```bash
docker run --name=phpunit --rm -it -d -v /path/to/your/Workspace/VdmLibraryBundle:/var/www/html 3slab/vdm-library-base:latest /bin/bash
docker exec -ti phpunit /bin/bash
composer install

./vendor/bin/phpunit Tests
```

