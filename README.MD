#Backpack Installer

###Install

```bash
composer global require gotrecillo/backpack-installer --prefer-dist
```

###Setup

We can configure it to use a database user to create the database used in the project and execute the migration and initial seeding.
```bash
backpack-installer config:set
```

To check the current configuration used:
```bash
backpack-installer config:list
```


###Create a new project
```bash
backpack-installer new ProjectName
```

Answer some questions, check the additional packages that you want to install and go for some coffee while it create the whole project.

When it is done.
```bash
cd ProjectName
php artisan server
```

Happy coding !
