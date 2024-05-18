<picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://banners.beyondco.de/Precondition.png?theme=dark&packageManager=composer+require&packageName=onursimsek%2Fprecondition&pattern=topography&style=style_2&description=HTTP+Precondition+for+Laravel&md=1&showWatermark=1&fontSize=125px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg">
  <source media="(prefers-color-scheme: light)" srcset="https://banners.beyondco.de/Precondition.png?theme=light&packageManager=composer+require&packageName=onursimsek%2Fprecondition&pattern=topography&style=style_2&description=HTTP+Precondition+for+Laravel&md=1&showWatermark=1&fontSize=125px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg">
  <img alt="Package Image" src="https://banners.beyondco.de/Precondition.png?theme=dark&packageManager=composer+require&packageName=onursimsek%2Fprecondition&pattern=topography&style=style_2&description=HTTP+Precondition+for+Laravel&md=1&showWatermark=1&fontSize=125px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg">
</picture>

# HTTP Precondition for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/onursimsek/precondition.svg?style=flat-square)](https://packagist.org/packages/onursimsek/precondition)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Tests](https://github.com/onursimsek/precondition/actions/workflows/run-tests.yml/badge.svg)](https://github.com/onursimsek/precondition/actions)
[![Total Downloads](https://img.shields.io/packagist/dt/onursimsek/precondition.svg?style=flat-square)](https://packagist.org/packages/onursimsek/precondition)

## Installation

To install, run the following command in your project:

```shell
composer require onursimsek/precondition
```

## Usage

A client has requested a resource from you (GET), and after a while wants to update the resource and send it back to
you (PUT). Meanwhile, another client has received the same resource (GET) and updated it before the first client (PUT).
In this case, the first client's update will be based on an incorrect copy and will ignore the second client's updates.
This is called **[lost update problem](https://www.rfc-editor.org/rfc/rfc6585.txt)**.

In such a case, you can return **[428 Precondition Required](https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/428)** to the first
client and ask it to pull the resource again.

This package allows you to do this easily. All you need to do is create a **Validator class** and add it as an 
**attribute** to the corresponding controller method.

```php
use Illuminate\Http\Request;
use OnurSimsek\Precondition\Validators\PreconditionValidator;

class LostUpdateValidator extends PreconditionValidator
{
    public function parameter(Request $request)
    {
        return $request->header('If-Unmodified-Since');
    }

    public function __invoke(Request $request): bool
    {
        return $this->parameter($request) == $request->route('article')->updated_at;
    }
}
```

```php
use App\Models\Article;
use App\Http\Requests\UpdateArticleRequest;
use OnurSimsek\Precondition\Attributes\Precondition;

class ArticleController
{
    #[Precondition(LostUpdateValidator::class)]
    public function update(Article $article, UpdateArticleRequest $request)
    {
        $article->fill($request->validated())
        $article->save();

        return response()->json();
    }
}
```

Finally, you need to add **precondition** middleware to the **route** you want to use.

```php
Route::put('/articles/{:article}', [ArticleController::class, 'update'])
    ->middleware(['precondition']);
```

You can also use this package if you have any conditions for a request to be fulfilled. For example some examples;

### Github Sample

You are prompted to re-type the repository name before deleting a repository.

```php
use Illuminate\Http\Request;
use OnurSimsek\Precondition\Validators\PreconditionValidator;

class DeleteRepositoryValidator extends PreconditionValidator
{
    public function parameter(Request $request)
    {
        return $request->input('repository_name');
    }

    public function __invoke(Request $request): bool
    {
        return $this->parameter($request) == $request->route('repository')->name;
    }
}
```

**Note:** If the repo name is invalid, the response returns with a status code of 
**[412 Precondition Failed](https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/412)**.

### SMS Verification

SMS verification is a mandatory step in the payment process.

```php
use Illuminate\Http\Request;
use OnurSimsek\Precondition\Validators\PreconditionValidator;

class SmsValidator extends PreconditionValidator
{
    public function preProcess(): void
    {
        cache()->set('sms', '123456');
    }

    public function parameter(Request $request)
    {
        return $request->input('sms_code');
    }

    public function __invoke(Request $request): bool
    {
        return $this->parameter($request) == cache()->get('sms');
    }
}
```

### Optional Verification

If you want to validate on a case-by-case basis, you can use the **when** method as follows.

```php
use Illuminate\Http\Request;
use OnurSimsek\Precondition\Validators\PreconditionValidator;

class PrivateArticleValidator extends PreconditionValidator
{
    public function when(Request $request) 
    {
        return $request->route('article')->is_private;
    }

    public function parameter(Request $request)
    {
        return $request->header('X-Article-Secret-Code');
    }

    public function __invoke(Request $request): bool
    {
        return $this->parameter($request) == $request->route('article')->secret_code;
    }
}
```

You can use the **messages()** method in the validator class to customize the error messages.

```php
public function messages(): array
{
    return [
        'required' => 'Enter the sms code sent to your phone.',
        'failed' => 'Incorrect sms code!',
    ];
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Onur Simsek](https://github.com/onursimsek)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
