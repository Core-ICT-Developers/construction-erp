<p align="center">
  <img width="200" src="https://www.coreict.co.ke/coreictconsultancy/public/images/Logo%20Resized.png">
</p>
<p align="center">
  <a href="https://www.coreict.co.ke">
    Developed by Core ICT Consultancy developers
  </a>
</p>

# Construction ERP
Construction ERP software centralizes and streamlines the entire construction project lifecycle so that project can maintain deadlines and keep on track.

Documentation: [https://www.coreict.co.ke/construction-erp](https://www.coreict.co.ke/construction-erp)

## Screenshot
<p align="center">
  <img width="900" src="https://www.coreict.co.ke/coreictconsultancy/public/images/Construction-erp.PNG">
</p>

## Getting started

### Prerequisites

 * Node. 
 * MySQL database.
 * PHP7.


### Installing
#### Manual

```bash
# Clone the project and run composer
composer update
cd construction

# Migration and DB seeder (after changing your DB settings in .env)
php artisan migrate --seed

# Install dependency with NPM
npm install

# develop
npm run dev # or npm run watch

# Build on production
npm run production
```

#### Docker
```sh
docker-compose up -d
```
Build static files within Laravel container with npm
```sh
# Get laravel docker container ID from containers list
docker ps

docker exec -it <container ID> npm run dev # or npm run watch
# Where <container ID> is the "laravel" container name, ex: src_laravel_1
```
Open http://localhost:8000 (laravel container port declared in `docker-compose.yml`) to access The Construction ERP

## Running the tests
* Tests system is under development

## Deployment and/or CI/CD
This project uses [Envoy](https://laravel.com/docs/5.8/envoy) for deployment, and [GitLab CI/CD](https://about.gitlab.com/product/continuous-integration/). Please check `Envoy.blade.php` and `.gitlab-ci.yml` for more detail.

## Built with
* [Laravel](https://laravel.com/) - The PHP Framework For Web Artisans
* [Laravel Sanctum](https://github.com/laravel/sanctum/) - Laravel Sanctum provides a featherweight authentication system for SPAs and simple APIs.
* [spatie/laravel-permission](https://github.com/spatie/laravel-permission) - Associate users with permissions and roles.
* [VueJS](https://vuejs.org/) - The Progressive JavaScript Framework
* [Element](https://element.eleme.io/) - A  Vue 2.0 based component library for developers, designers and product managers
* [Vue Admin Template](https://github.com/PanJiaChen/vue-admin-template) - A minimal vue admin template with Element UI



## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, please look at the [release tags](https://github.com/tuandm/laravue/tags) on this repository.

## Authors

* **Mugambi M. Mundia** - *Initial work* - [MartinDeMundia](https://github.com/MartinDeMundia).


## Donate
If you find this construction erp project useful, you can [buy me a pizza](https://www.buymeacoffee.com/mugambidemundia)
