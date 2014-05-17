# Hydrator

This component is a prototype for Zend Framework 3 hydrator component, that you can use today for new projects (see
below for more details). Compared to original implementation, it offers:

* A massive performance boost (this new implementation is between 5-10 times faster than current one)
* Cleaner (a lot of features that were hacked throughout ZF2 life have been integrated from the beginning).
* Provides built-in strategies for common use cases (as of now, `DateStrategy` has well as a hydrator that allows
nested hydrators).
* Some interfaces have been modified to be more explicit (see `NamingStrategyInterface` for instance).
* Contexts are handled in a more homogeneous way through two new context objects: `ExtractionContext` and
`HydrationContext`. In ZF2, you could only get the hydrated array data during hydration phase, now you can also
have access to the hydrated object.

## Using in existing/new projects

This library can be used in any projects. If you are a Zend Framework 2 user, you can actually use it now in new
projects, by providing the following configuration:

```php
use Hydrator\Factory\HydratorPluginManagerFactory;

return [
    'service_manager' => [
        'factories' => [
            'HydratorManager' => HydratorPluginManagerFactory::class
        ]
    ]
];
```

### Limitations

Current implementation has some compatibility layer to make it compatible with ZF2. For example, this library's
hydrator interface extends `Zend\Stdlib\Hydrator\HydratorInterface`. Furthermore, the hydrator plugin manager allows
you to create both `Hydrator\HydratorInterface` (new) and `Zend\Stdlib\Hydrator\HydratorInterface` (old).

Actually, if your third-party modules or other ZF2 components are using hydrators in a very simple way (using
`extract` and `hydrate` method), this library could be used as a drop-in with very few changes.

However, as soon as the code uses more advanced features like naming strategies, custom filters or strategies,
there is great chance that it won't work because of conflicting interfaces. You are therefore encouraged to
carefully test your code before using this library.
