# Hydrator

[![Build Status](https://travis-ci.org/zf-fr/hydrator.png?branch=master)](https://travis-ci.org/zf-fr/hydrator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/zf-fr/hydrator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/zf-fr/hydrator/?branch=master)

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
* All built-in hydrators are marked as final

## Using in existing/new projects

This library can be used in any projects. If you are a Zend Framework 2 user, you can actually use it now in new
projects, by providing the following configuration:

```php
use Hydrator\HydratorPluginManager;
use Hydrator\Factory\HydratorPluginManagerFactory;

return [
    'service_manager' => [
        'factories' => [
            HydratorPluginManager::class => HydratorPluginManagerFactory::class
        ]
    ]
];
```

You can set hydrators in a similary way that ZF2 hydrators, but by using the "zfr_hydrators" key instead of
"hydrators".

### Limitations

Current implementation has some compatibility layer to make it compatible with ZF2. For example, this library's
hydrator interface extends `Zend\Stdlib\Hydrator\HydratorInterface`. Furthermore, the hydrator plugin manager allows
you to create both `Hydrator\HydratorInterface` (new) and `Zend\Stdlib\Hydrator\HydratorInterface` (old).

Actually, if your third-party modules or other ZF2 components are using hydrators in a very simple way (using
`extract` and `hydrate` method), this library could be used as a drop-in with very few changes.

However, as soon as the code uses more advanced features like naming strategies, custom filters or strategies,
there is great chance that it won't work because of conflicting interfaces. You are therefore encouraged to
carefully test your code before using this library.

### Why built-in hydrators are final, and how should I use them?

This is actually not a decision for ZF3, but just an experiment. We decided to make all built-in hydrators as final. This
is not a well-known PHP keyword, but basically, it means you cannot extend them.

The reason behind this choice is performance. By being sure that no one can extend them, we can do more aggresive
optimizations (like caching). This leads to dramatic performance improvements, especially for collection of objects.

The question is how to do things like filtering properties? If you used to extend ZF2 ClassMethods hydrator, you have
already written such code:

```php
class UserHydrator extends ClassMethods
{
    public function __construct()
    {
        parent::__construct();

        $this->filterComposite->addFilter('password', new MethodMatchFilter('getPassword'), FilterComposite::CONDITION_AND);
    }
}
```

With this setup, the "getPassword" method is never called by the hydrator, so this field is never extracted. With this
prototype, your hydrator actually *composes* a ClassMethods hydrator.

```php
class UserHydrator implements HydratorInterface
{
    protected $hydrator;

    public function __construct()
    {
        $this->hydrator = new ClassMethods();
    }

    public function extract($object)
    {
        $values = $this->hydrator->extract($object);

        unset($values['password'], $values['anotherValue']);

        return $values;
    }

    public function hydrate(array $data, $object)
    {
        return $this->hydrator->hydrate($data, $object);
    }
}
```

While this is more verbose, the rationale is that removing values is actually business specific. You will likely have
to stripe the `password` value ONLY for the UserHydrator.

Furthermore, this allows us to performs more optimizations. For instance:

* Because hydrators like `ArraySerializableHydrator` cannot be extended, we can know for sure that the filter
feature is useless for this one, so it is completely removed (which makes this hydrator magnitudes faster than
ZF2's one).
* For the ClassMethods hydrator, we can aggressively cache a lot of things, so that if you run the same hydrators
for two different objects, the second iteration will be MUCH faster. This is only possible because of the final, as
you could have context dependant filter's that would make it nearly impossible to use such aggressive caching.

Of course, because of PHP 5.4 traits, you could quite easily recreate your own inheritable ClassMethods hydrator (with
`ProvidesNamingStrategyTrait` and `ProvidesStrategiesTrait` you can write it in a few minutes ;).
