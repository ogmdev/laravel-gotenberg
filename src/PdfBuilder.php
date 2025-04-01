<?php

namespace SaferMobility\LaravelGotenberg;

use Closure;
use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\Modules\ChromiumPdf;
use Gotenberg\Stream;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\RequestInterface;
use SaferMobility\LaravelGotenberg\Enums\Format;
use SaferMobility\LaravelGotenberg\Enums\Orientation;
use SaferMobility\LaravelGotenberg\Enums\Unit;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PdfBuilder implements Responsable
{
    public string $viewName = '';

    public array $viewData = [];

    public string $html = '';

    public string $headerViewName = '';

    public array $headerData = [];

    public ?string $headerHtml = null;

    public string $footerViewName = '';

    public array $footerData = [];

    public ?string $footerHtml = null;

    public string $downloadName = '';

    public string $disposition = 'inline';

    public ?array $paperSize = null;

    public ?string $orientation = null;

    public ?array $margins = null;

    protected string $visibility = 'private';

    protected ?Closure $customizeGenerator = null;

    protected array $responseHeaders = [
        'Content-Type' => 'application/pdf',
    ];

    protected bool $onLambda = false;

    protected ?string $diskName = null;

    public function view(string $view, array $data = []): self
    {
        $this->viewName = $view;

        $this->viewData = $data;

        return $this;
    }

    public function headerView(string $view, array $data = []): self
    {
        $this->headerViewName = $view;

        $this->headerData = $data;

        return $this;
    }

    public function footerView(string $view, array $data = []): self
    {
        $this->footerViewName = $view;

        $this->footerData = $data;

        return $this;
    }

    public function landscape(): self
    {
        return $this->orientation(Orientation::Landscape);
    }

    public function portrait(): self
    {
        return $this->orientation(Orientation::Portrait);
    }

    public function orientation(string|Orientation $orientation): self
    {
        if ($orientation instanceof Orientation) {
            $orientation = $orientation->value;
        }

        $this->orientation = $orientation;

        return $this;
    }

    public function inline(string $downloadName = ''): self
    {
        $this->name($downloadName);

        $this->disposition = 'inline';

        return $this;
    }

    public function html(string $html): self
    {
        $this->html = $html;

        return $this;
    }

    public function headerHtml(string $html): self
    {
        $this->headerHtml = $html;

        return $this;
    }

    public function footerHtml(string $html): self
    {
        $this->footerHtml = $html;

        return $this;
    }

    public function download(?string $downloadName = null): self
    {
        $this->downloadName ?: $this->name($downloadName ?? 'download');

        $this->disposition = 'attachment';

        return $this;
    }

    public function headers(array $headers): self
    {
        $this->addHeaders($headers);

        return $this;
    }

    public function name(string $downloadName): self
    {
        if (! str_ends_with(strtolower($downloadName), '.pdf')) {
            $downloadName .= '.pdf';
        }

        $this->downloadName = $downloadName;

        return $this;
    }

    public function base64(): string
    {
        $content = Http::post($this->getGenerator())->body();

        return base64_encode($content);
    }

    public function margins(
        float $top = 0,
        float $right = 0,
        float $bottom = 0,
        float $left = 0,
        Unit|string $unit = 'in'
    ): self {
        if (!$unit instanceof Unit) {
            $unit = Unit::from($unit);
        }

        $this->margins = compact(
            'top',
            'right',
            'bottom',
            'left',
            'unit',
        );

        return $this;
    }

    public function format(string|Format $format): self
    {
        if (! $format instanceof Format) {
            $format = Format::from($format);
        }

        $this->paperSize(...$format->pageSize());

        return $this;
    }

    public function paperSize(float $width, float $height, Unit|string $unit = 'in'): self
    {
        if ($unit instanceof Unit) {
            $unit = $unit->value;
        }

        $this->paperSize = compact(
            'width',
            'height',
            'unit',
        );

        return $this;
    }

    public function customize(callable $callback): self
    {
        $this->customizeGenerator = $callback;

        return $this;
    }

    public function onLambda(): self
    {
        $this->onLambda = true;

        return $this;
    }

    public function save(string $path): self
    {
        if ($this->diskName) {
            return $this->saveOnDisk($this->diskName, $path);
        }

        $response = Http::post($this->getGenerator())->resource();

        $file = fopen($path, 'w');
        if ($file === false) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        // Streaming for large files, instead of loading the whole thing into memory
        while (! feof($response)) {
            $chunk = fread($response, 1024 * 512); // read up to 0.5 MiB at a time
            if (fwrite($file, $chunk) === false) {
                throw NativeFunctionErrored::createFromLastPhpError();
            }
        }
        fclose($response);
        if (fclose($file) === false) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        return $this;
    }

    public function disk(string $diskName, string $visibility = 'private'): self
    {
        $this->diskName = $diskName;
        $this->visibility = $visibility;

        return $this;
    }

    protected function saveOnDisk(string $diskName, string $path): self
    {
        $content = Http::post($this->getGenerator())->toPsrResponse()->getBody();

        $visibility = $this->visibility;

        Storage::disk($diskName)->put($path, $content, $visibility);

        return $this;
    }

    protected function getHtml(): string
    {
        if ($this->viewName) {
            $this->html = view($this->viewName, $this->viewData)->render();
        }

        if ($this->html) {
            return $this->html;
        }

        return '&nbsp';
    }

    protected function getHeaderHtml(): ?string
    {
        if ($this->headerViewName) {
            $this->headerHtml = view($this->headerViewName, $this->headerData)->render();
        }

        if ($this->headerHtml) {
            return $this->headerHtml;
        }

        return null;
    }

    protected function getFooterHtml(): ?string
    {
        if ($this->footerViewName) {
            $this->footerHtml = view($this->footerViewName, $this->footerData)->render();
        }

        if ($this->footerHtml) {
            return $this->footerHtml;
        }

        return null;
    }

    protected function getAllHtml(): string
    {
        return implode(PHP_EOL, [
            $this->getHeaderHtml(),
            $this->getHtml(),
            $this->getFooterHtml(),
        ]);
    }

    public function getGenerator(): RequestInterface
    {
        $generator = new ChromiumPdf(config('gotenberg.host'));

        $generator->printBackground();

        $headerHtml = $this->getHeaderHtml();

        $footerHtml = $this->getFooterHtml();

        if ($headerHtml) {
            $generator->header(Stream::string('', $headerHtml));
        }

        if ($footerHtml) {
            $generator->footer(Stream::string('', $footerHtml));
        }

        if ($this->margins) {
            $generator->margins(
                top: $this->margins['top'].$this->margins['unit'],
                bottom: $this->margins['bottom'].$this->margins['unit'],
                left: $this->margins['left'].$this->margins['unit'],
                right: $this->margins['right'].$this->margins['unit'],
            );
        }

        if ($this->paperSize) {
            $generator->paperSize(
                width:  $this->paperSize['width']  . $this->paperSize['unit'],
                height: $this->paperSize['height'] . $this->paperSize['unit'],
            );
        }

        if ($this->orientation === Orientation::Landscape->value) {
            $generator->landscape();
        }

        if ($this->customizeGenerator) {
            ($this->customizeGenerator)($generator);
        }

        return $generator->html(Stream::string('', $this->getHtml()));
    }

    public function toResponse($request): StreamedResponse
    {
        $stream = Http::post($this->getGenerator())->toPsrResponse()->getBody();

        // Partially based on https://github.com/laravel/framework/discussions/49991
        return response()->streamDownload(
            function () use ($stream) {
                while (! feof($stream)) {
                    echo $stream->read(1024 * 512); // 0.5 MiB chunks
                }
                $stream->close();
            },
            $this->downloadName,
            $this->responseHeaders,
            $this->disposition,
        );
    }

    protected function addHeaders(array $headers): self
    {
        $this->responseHeaders = array_merge($this->responseHeaders, $headers);

        return $this;
    }

    public function contains(string|array $text): bool
    {
        if (is_string($text)) {
            $text = [$text];
        }

        $html = $this->getAllHtml();

        foreach ($text as $singleText) {
            if (str_contains($html, $singleText)) {
                return true;
            }
        }

        return false;
    }
}
