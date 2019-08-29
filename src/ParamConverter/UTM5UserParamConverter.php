<?php
declare(strict_types=1);

namespace App\ParamConverter;


use App\Entity\UTM5\UTM5User;
use App\Service\UTM5\UTM5DbService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class UTM5UserParamConverter implements ParamConverterInterface
{
    public const CONVERTER_NAME = 'UTM5User';
    private const CONVERTER_CLASS = UTM5User::class;
    public const ID_KEY_NAME = "id";

    /** @var Request */
    private $requst;
    /** @var ParamConverter */
    private $configuration;
    /** @var UTM5DbService  */
    private $UTM5DbService;
    /** @var TranslatorInterface  */
    private $translator;

    public function __construct(UTM5DbService $UTM5DbService, TranslatorInterface $translator)
    {
        $this->UTM5DbService = $UTM5DbService;
        $this->translator = $translator;
    }

    public function apply(Request $request, ParamConverter $configuration): void
    {
        $this->configuration = $configuration;
        $this->request = $request;

        try {
            $UTM5User = $this->UTM5DbService->search(
                (string)$this->getParameter(),
                UTM5DbService::SEARCH_TYPE_ID);
            $request->attributes->set($configuration->getName(), $UTM5User);
        } catch (\DomainException $e) {
            throw new NotFoundHttpException($e->getMessage());exit;
        }
    }

    private function getParameter(): int
    {
        if($this->request->attributes->has($parameter = $this->getParameterName())) {
            return $this->request->attributes->getInt($parameter);
        }
        throw new \InvalidArgumentException($this->translator->trans("Required attribute %parameter% not found", ['%parameter%' => $parameter]));
    }

    private function getParameterName(): string
    {
        if (array_key_exists(self::ID_KEY_NAME, $options = $this->configuration->getOptions())) {
            return $options[self::ID_KEY_NAME];
        }
        throw new \InvalidArgumentException($this->translator->trans("Id key attribute name not found"));
    }

    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getName() === self::CONVERTER_NAME &&
            $configuration->getClass() === self::CONVERTER_CLASS;
    }
}