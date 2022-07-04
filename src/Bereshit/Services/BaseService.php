<?php

namespace Bereshit\Services;

use Bereshit\Contracts\ServiceContract;
use Bereshit\Repositories\BaseRepository;
use Bereshit\Http\Resources\GenericCollection;
use Bereshit\Http\Resources\GenericResource;
use Bereshit\Validations\BaseValidation;

abstract class BaseService implements ServiceContract
{
    /**
     * @var BaseRepository $repository
     */
    protected $repository;

    protected $jsonResource;

    /**
     * @var string $jsonCollection
     */
    protected $jsonCollection;

    /**
     * @var array $validationMessages
     */
    protected $validationMessages;

    /**
     * @var object $validationRequestClass
     */
    protected $validationRequestClass;

    /**
     * @var BaseValidation $validationClass
     */
    protected $validationClass;

    /**
    * @var string $name
    */
    protected $name;

    /**
     * Instantiate a new service instance.
     *
     * @param BaseRepository $repository
     * @param BaseValidation $validationClass
     * @return void
     */
    public function __construct(BaseRepository $repository, $validationClass)
    {
        $this->repository = $repository;
        $this->jsonCollection = GenericCollection::class;
        $this->jsonResource = GenericResource::class;
        $this->validationClass = $validationClass;
        $this->validationMessages = [
            'notFound' => 'notFound',
            'isNotId'  => 'isNotId'
        ];
        $this->validationRequestClass = (object)[
            'save' => '',
            'update' => '',
            'options' => ''
        ];
        $this->name = 'registro';
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function all($filters = [])
    {
        $this->jsonResource = $this->jsonResource['all'] ?? $this->jsonResource;
        $this->checkExistsJsonResourceClass();
        return $this->collection($this->repository->all($filters), $this->jsonResource, $filters);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws Exception
     */
    public function find($id)
    {
        $result = $this->findObjectInRepository($id);
        try {
            return $this->resource($result, 'find');
        } catch (\Throwable $e) {
            throw new \Exception($e);
        }
    }

    /**
     * @param int $id
     * @return mixed
     * @throws Exception
     */
    public function delete($id)
    {
        $this->findObjectInRepository($id);
        $this->repository->beginTransaction();
        try {
            $destroyed = $this->repository->destroy($id);
            $this->repository->commit();

            return $destroyed;
        } catch (\Throwable $e) {
            $this->repository->rollBack();
            throw new \Exception($e);
        }
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    public function save($data)
    {
        $this->executeValidations($data);
        return $this->runSaveTransaction($data);
    }


    /**
     * @param mixed $data
     * @param int $id
     * @return mixed
     */
    public function update($data, $id)
    {
        $this->findObjectInRepository($id);
        $this->executeValidations($data, $id);
        return $this->runSaveTransaction($data, $id);
    }

    /**
     * @param int $id
     * @throws Exception
     * @return mixed
     */
    public function findObjectInRepository($id)
    {
        /** @phpstan-ignore-next-line */
        if (!is_numeric($id) || $id === NULL) {
            $isNotId = $this->validationMessages['isNotId'] ?? 'isNotId';
            throw new \Exception($isNotId);
        }

        $model = $this->repository->find($id);
        $notFoundMessage = $this->validationMessages['notFound'] ?? $this->validationMessages;
        $this->validationClass->exists($model, $notFoundMessage);
        return $model;
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    public function resource($data, $method = 'store')
    {
        $this->jsonResource = $this->jsonResource[$method] ?? $this->jsonResource;
        $this->checkExistsJsonResourceClass();
        return new $this->jsonResource($data);
    }

    /**
     * @param mixed $data
     * @param mixed $resource
     * @param mixed $request
     * @return mixed
     */
    public function collection($data, $resource = null, $request = null)
    {
        $resource = $resource ?? $this->jsonResource;
        $collection = $this->jsonCollection ?? GenericCollection::class;

        return (new $collection($data))
            ->setResource($resource)
            ->toArray($request);
    }

    /**
     * @param mixed $data
     * @param mixed $id
     * @return array
     * @throws Exception
     */
    public function runSaveTransaction($data, $id = null)
    {
        $this->repository->beginTransaction();

        try {
            if ($id === NULL) {
                $data  = $this->resource($data)->toFormat();
                $dados = $this->repository->store($data);
            } else {
                $data = $this->resource($data, 'update')->toFormat(true);
                $dados = $this->repository->update($data, $id);
            }
            $this->repository->commit();
            return $this->resource($dados)->toArray($data);
        } catch (\Throwable $e) {

            $this->repository->rollBack();
            throw new \Exception($e);
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function checkExistsJsonResourceClass()
    {
        if (class_exists($this->jsonResource)) {
            return true;
        } else {
            throw new \Exception(NULL, ('undefinedClass'));
        }
    }

    /**
     * Valida dados da requisição com o FormRequest específico
     *
     * @param mixed $data
     * @param mixed $id
     * @return void
     */
    protected function executeValidations($data, $id = null)
    {
        if (!is_null($id)) {
            $this->validationClass->passes($this->validationRequestClass->update, $data);
        } else {
            $this->validationClass->passes($this->validationRequestClass->save, $data);
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function setValidation(BaseValidation $validation)
    {
        $this->validationClass = $validation;
    }

    public function getValidation()
    {
        return $this->repository;
    }

    public function setRepository(BaseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getRepository()
    {
        return $this->repository;
    }
}