# Changelog

## 1.0.43 - 2023.06.26

- Added: `Rdate` helper class add `numberOfCurrentWeek` method

## 1.0.42 - 2023.06.20

- Optimized: `validateRules` default message support mutipi language config

## 1.0.41 - 2023.06.19

- Optimized: `helper` class `lang`, support $language is empty

## 1.0.40 - 2023.06.16

Added: `ValidateRules` add `decimal` for judge the decimal precision

## 1.0.39 - 2023.06.08

- Fixed: `DB` core class, handlestring for where

## 1.0.38 - 2023.06.07

- Optimized: `Rdate` helper class `currentWeek` method
- Optimized: `DB` core class `insertAll` method

## 1.0.37 - 2023.06.06

- Added: `Rdate` helper class add `currentWeek` method

## 1.0.36 - 2023.06.05

- Added: `helper` add `__` method, you can use it to get the language text
- Added: `filesystem` config file
- Added: `UploadFiles` helper class and `uploader` tool
- Optimized: framework code

## 1.0.35 - 2023.06.02

- Fixed: `model` class getAll will return all rows by default

## 1.0.34 - 2023.05.30

- Improved: `validator` class, Optimize the code and add the default message

## 1.0.33 - 2023.05.30

- Fixed: `validate_time` Support time format like: `000:00`
- Added: `DB` class add multi method(sum, insertAll, updateAll, truncate, etc...)
- Added: `Model` class add multi method(like to `DB`)
- Note: Added methods are not actually tested

## 1.0.32 - 2023.05.29

- Improved: `Router` class(Support soft router with `_`)

## 1.0.31 - 2023.05.26

- Added: `Model` class add `sql` function(you can get the sql query statement)
- Fixed: `DB` class Add "`" symbols on both sides of the field to avoid SQL keyword conflicts
  include all table name and field name
- Added: index add RISEN_START, You can use it to count the time spent by the program running,
  it's interesting, isn't it
- Added: validaterules add time and double rules function
- Added: `helper` add `lang` class and `Rdate` class

## 1.0.3 - 2023.05.25

- Added: index page add version
- Optimize: `DB` class(Where support custom string)

## 1.0.2 - 2023.05.24

- Added: Request class functions(getBody, contentType, isAjax)
- Fixed: HttpRequest class(support body include file path)
- Upgraded: UploadFiles class(support more params)

## 1.0.1 - 2023.05.23

- Fixed: ValidateRules class validate_required
  (In the previous version, if the value passed in was 0, the wrong return failed)
- Fixed: Model class all function (not useful before)
- Upgraded: HttpRequest class (support file upload)
- Added: ValidateRules validate_phone
- Added: Registry class (used to store and access shared data or resources)

## 1.0.0 - 2023.05.18

- upgrade framework version

## 0.4.2 - 2023.04.26

- optimize: `DB` class
- Added: core `Model` class

## 0.4.1 - 2023.04.24

- Change the src directory to an extended directory and move out of autoload.php
  (In order to separate the plug-in part in the future)
- Added: src/rjwt (JWT class)
- Changed the cache and log directories, they are now placed in the storage directory
- Added: env helper function, if the env configuration is enabled, the environment configuration will be read first
- upgrade: core db class
- Modify the catch output of Exception and remove the message module

## 0.4.0 - 2023.04.21

- Upgraded CoreRouter class (support middle)
- Add Cache (config and routes can be cached)
- Add many tools
- Optimize many codes
- Fixed many bugs
- Add SMTP Emali Function

## 0.3.0 - 2023.04.07

- Upgraded DB class

- Modified the file directory structure (libs is placed in the root directory)

- Change the apiout class to message, and redefine the output format

## 0.2.0

- Risen init
