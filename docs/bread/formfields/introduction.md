# Formfields

Lalelu

## Overview

This table gives you an overview of all built-in formfields and their recommended column type

| **Formfield**                       | **Description**                                      | **Recommended column type**                |
|-------------------------------------|------------------------------------------------------|--------------------------------------------|
| [Checkbox](checkbox.md)             | Check one or many given options                      | JSON*                                      |
| [Dynamic select](dynamic-select.md) | Select one or many options from a user generated set | Depending on your resulting key(s)         |
| [Media picker](media-picker.md)     | Select one or many files with the media manager      | JSON*                                      |
| [Number](number.md)                 | Enter a number, float or double                      | Int, Float, Double                         |
| [Password](password.md)             | A password formfield                                 | Text, Varchar                              |
| [Radio](radio.md)                   | Select one of many given options                     | Text, Number, ... (depending on your keys) |
| [Select](select.md)                 | Select one or multiple given options                 | Text, Varchar, JSON                        |
| [Simple array](simple-array.md)     | Enter multiple values of any kind                    | JSON*                                      |
| [Slug](slug.md)                     | Generate a slug from a given formfield               | Text, Varchar                              |
| [Tags](tags.md)                     | Allows you to enter tags                             | JSON*                                      |
| [Text](text.md)                     | A standard text formfield                            | Text, Longtext, Varchar                    |


*) Formfields with an asterisk **require** the column to be real JSON as the result is always an array.