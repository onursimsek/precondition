# HTTP Precondition for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/onursimsek/precondition.svg?style=flat-square)](https://packagist.org/packages/onursimsek/precondition)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Tests](https://github.com/onursimsek/precondition/actions/workflows/run-tests.yml/badge.svg)](https://github.com/onursimsek/precondition/actions)
[![Quality Score](https://img.shields.io/scrutinizer/g/onursimsek/precondition.svg?style=flat-square)](https://scrutinizer-ci.com/g/onursimsek/precondition)
[![Total Downloads](https://img.shields.io/packagist/dt/onursimsek/precondition.svg?style=flat-square)](https://packagist.org/packages/onursimsek/precondition)

## Kurulum

Kurulum icin projenizde asagidaki komutu calistirin:

```shell
composer require onursimsek/precondition
```

## Kullanim

Bir istemci sizden bir kaynagi istemis (GET), bir sure sonra kaynakta guncelleme yapip size geri gondermek (PUT) ister.
O sirada baska bir istemci de ayni kaynagi almis (GET) ve ilk istemciden once guncellemistir (PUT). Bu durumda ilk
istemcinin yapmis oldugu guncelleme yanlis bir kopya uzerinden olacaktir ve ikinci istemcinin yaptigi guncellemeleri
gormezden gelecektir. Buna **[kayip guncelleme problemi](https://www.rfc-editor.org/rfc/rfc6585.txt)** denir.

Boyle bir durumda ilk istemciye **[428 Precondition Required](https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/428)** donusunde
bulunarak, kaynagi tekrar cekmesini isteyebilirsiniz.

Bu paket bu islemi kolayca yapmanizi saglar. Tek yapmaniz gereken bir **Validator class**'i olusturmak ve ilgili
controller metoduna **attribute** olarak eklemek.

```php
use Illuminate\Http\Request;
use OnurSimsek\Precondition\Validators\PreconditionValidator;

class LostUpdateValidator extends PreconditionValidator
{
    public function parameter(Request $request)
    {
        return $request->header('If-Match');
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

Bunun disinda bir istegin gerceklestirilmesi icin herhangi bir sartiniz varsa yine bu paketi kullanabilirsiniz. Iste
bazi ornekler;

### Github Ornegi

Bir repo silinmeden once repo adinin tekrar yazilmasi istenir.

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

**Not:** Repo adi dogru degilse, cevap 
**[412 Precondition Failed](https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/412)** durum kodu ile doner.

### SMS ile Dogrulama

Odeme islemi yapilmadan once sms dogrulamasi yapilmak istenir.

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
        return cache()->get('sms');
    }

    public function __invoke(Request $request): bool
    {
        return $this->parameter($request) == $request->input('sms_code');
    }
}
```

Hata mesajlarini ozellestirmek icin validator class'inda **messages()** metodunu kullanabilirsiniz.

```php
public function messages(): array
{
    return [
        'required' => 'Telefonunuza gelen sms kodunu giriniz.',
        'failed' => 'Sms kodunu dogru girmediniz!',
    ];
}
```
