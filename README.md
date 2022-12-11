# ModelConstructorBundle

# This bundle is in WIP

## How to use it

To create a model create a class that extend the AbstractModel.

And implement their Mapping.

Example :

```php
<?php

class UserModel extends AbstractModel {
    private int $id = 0;
    private string $username = '0';
    private SettingModel $setting;
    private ArrayObject $friends;

    public function getMapping()
    {
        return [
            'id'        => [
                'path' => 'id',
                'cast' => 'int'
            ],
            'username'  => 'name',
            'setting'   => [
                'path'         => 'user_setting',
                'class'        => SettingModel::class,
                'isCollection' => false,
            ],
            'friends'   => [
                'path'         => 'other.friends',
                'class'        => UserModel::class,
                'isCollection' => true,
            ],
        ];
    }
    
    ### Your getter (and setter if needed)
}

class SettingModel extends AbstractModel {
    private string $appearance = 'dark';

    public function getMapping()
    {
        return [
            'appearance' => 'appearance'
        ];
    }
    
    ### Your getter (and setter if needed)
}
```
