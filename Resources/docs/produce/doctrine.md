# Store Doctrine

## Configuration reference

There are two parts ton configure: the transport, and Doctrine's behaviour.

### Transport

In `messenger.yaml`:

```yaml
framework:
    messenger:
        transports:
            producer:
                dsn: vdm+doctrine://
                options:
                    entity: App\Entity\Demande
                    selector: RefDemande
```

Configuration | Description
--- | ---
dsn | Will always be vdm+doctrine://
options.entity | Fully Qualified Class Name of the entity the transport is about
options.selector | (optional) Define how the sender will try and fetch a pre-existing entity before persisting (see below)

### Bundle configuration

The sender's policy is never to overwrite a non-null field with null data (when you're updating an existing entity for instance). In `vdm_library.yaml`, you can white-list which fields for which entity may be overwritten with null. Here is an example.

```yaml
vdm_library:
    doctrine:
        connection: default
        nullable_fields_whitelist:
            App\Entity\Demande:
                - NomContact
                - CodeTypeContact
                - LibelleTypeContact
                - NumeroAbonne
                - CodeCabinet
                - CodePoleContact
                - LibellePoleContact
                - EmailContact
                - PhoneContact
                - RefExterne
                - ComplementInformation
            App\Entity\Bar:
                - foo
                - bar
                - baz
```

Configuration | Description
--- | ---
connection | name of the  connection to use (fits into `doctrine.orm.xxx_entity_manager`). If you use the default connection, you can skip this parameter.
nullable_fields_whitelist | Dictionnary of entity FQCN => field[] that define which can be overwritten with null.

Here, the `App\Entity\Demande` entity defines 11 properties that can be overwritten with null, where as the `App\Entity\Bar` entity defines three.

Configuration | Description
--- | ---
dsn | Will always be vdm+doctrine://
options.entity | Fully Qualified Class Name of the entity the transport is about
options.selector | (optional) Define how the sender will try and fetch a pre-existing entity before persisting (see below)

## Fetching pre-existing entity

Before persisting anything, this transport will always try to find an existing entity. You need to tell it how to proceed. You have several ways of doing it.

### The natural way

It means that your entity bears a unique identifier value, such as:
```php
    /**
     * @ORM\Id()
     */
    private $id;
```

If this value is carried by the incoming message, then you have nothing to configure. The only responsability on your end is making sure there is a public getter for this property (if there isn't you'll get a clear error message anyway).

__Note__: in this case, the sender will use the  `find` method on the repository.

### Multifield with natural getters

In case you don't have a mono-column primary key (ex: no key at all or composite key), you can turn to another approach and tell the sender which fields should be used to retrieve a pre-existing entity. For instance, if your entity has two fields representing its identity (let's say `code` and `hash`), and they both have a natural getter (i.e. `getCode` and `getHash`), then you need to configure the options like this:

```yaml
framework:
    messenger:
        transports:
            producer:
                dsn: vdm+doctrine://
                options:
                    entity: App\Entity\Demande
                    selector:
                        - code
                        - hash
```

Under the hood, the repository will be called like:
```php
    $repo->findOneBy([ 'code' => $yourEntity->getCode(), 'hash' => $yourEntity->getHash() ])
```

__Note__: Notice the `findOneBy`. The sender will use the first matching entity. It's your responsability to provide a unique set of filter.

### Multifield with non-natural getters

In case the fields related to the identity have unatural getters (ex: legacy code, multilingual code), you can define which getter to use to fetch the appropriate property. Let's say the identity is made of two fields: `label` and `hash`, which respective getters are `getLibelle()` and `hash()`. You will need configure the sender as such:

```yaml
framework:
    messenger:
        transports:
            producer:
                dsn: vdm+doctrine://
                options:
                    entity: App\Entity\Demande
                    selector:
                        label: getLibelle
                        hash: hash
```

Under the hood, the repository will be called like:
```php
    $repo->findOneBy([ 'label' => $yourEntity->getLibelle(), 'hash' => $yourEntity->hash() ])
```

The same policy as natural getters apply: you have to make sure it returns something as unique as possible.

Finally, you can mix natural and unnattural getters:
```yaml
framework:
    messenger:
        transports:
            producer:
                dsn: vdm+doctrine://
                options:
                    entity: App\Entity\Demande
                    selector:
                        code
                        label: getLibelle
                        hash: hash
```

Under the hood, the repository will be called like:
```php
    $repo->findOneBy([ 'code' => $yourEntity->getCode(), 'label' => $yourEntity->getLibelle(), 'hash' => $yourEntity->hash() ])
```
