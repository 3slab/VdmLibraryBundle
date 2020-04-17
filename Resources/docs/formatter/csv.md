# CSV formatter

When the source of your transport returns csv files, the `AbstractCsvFormatter` may be helpful.

This class will handle a generic csv parsing and yield the row indidivually for your Message handler. By default rows are left unaltered and it yields an array where the indexes part is the header line and the values part is the row being read.

Consider the following file :
```
    Id;code:label
    1;foo;Foo
    2;bar;Bar
```

If will yields the following arrays:
```php
    // 1st pass
    yield [
        'Id'    => 1,
        'code'  => 'foo',
        'label' => 'Foo',
    ];

    // 2nd pass
    yield [
        'Id'    => 2,
        'code'  => 'bar',
        'label' => 'Bar',
    ];
```

## Implementing on your end

Your project should provide at least class that extends the `AbstractCsvFormatter` and define the `supports()` method. There are two ways of doing it:

- Define a constant FILE_NAME in your implementation that contains the file name (in case it never changes).
- Override the supports() method altogether if you need something mode complex (match against a regex in case the filename is timestamped for instance).

If the example above suits your need, you don't have anything more to do. But if you need to tweak the values or compute other ones, you can override the `getRow()` method. It will be passed the whole row as argument. You should return an array, that will be passed as payload to the Message generated.

**NOTE**: the csv headers are accessible with $this->headers.

### Example

The array I want to send is the original csv row, but reversed and uppercase. My formatter shold look like this:
```php
class MyFormatter extends AbstractCsvFormatter
{
    protected const FILE_NAME = 'foo.csv';

        /**
     * {inheritdoc}
     */
    protected function getRow(array $data): array
    {   
        $newData = [];

        foreach ($data as $k => $v) {
            $newData[$k] = strtoupper(strrev($v));
        }

        return $newData;
    }   
}
```

And the MessageHandler should look like this:
```php
class MessageHandler implements MessageSubscriberInterface
{
    // ...

    /**
     * @param Message $message
     */
    public function __invoke(Message $message): void
    {
        $pathToCsvFile = // ...

        foreach ($formatter->readFile($pathToCsvFile) as $row) {
            $message = new Message($row);

            $this->bus->dispatch($message);
        }
    }

    // ...
}
```