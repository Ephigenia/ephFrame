<?php

/**
 * Component that does Scaffolding
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 2010-07-16
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 */
class Scaffold extends Component
{
	public $form;
	
	public $model;
	
	public function startUp()
	{
		if (!isset($this->model)
			&& isset($this->controller->{$this->controller->name})
			&& $this->controller->{$this->controller->name} instanceof Model
			) {
			$this->model = &$this->controller->{$this->controller->name};
		}
		if (isset($this->model) && !isset($this->form)) {
			try {
				$this->controller->addForm($this->model->name.'Form');
				$this->form = &$this->controller->{$this->model->name.'Form'};
				$this->controller->data->set('form', $this->form);
			} catch (ephFrameClassFileNotFoundException $e) {
			}
		}
		return parent::startUp();
	}
	
	public function index()
	{
		if (!isset($this->model)) {
			return true;
		}
		$page = intval((@$this->params['page'] > 1) ? $this->params['page'] : 1);
		$perPage = $this->model->perPage;
		$entries = $this->model->findAll(null, null, ($page-1) * $perPage, $perPage);
		$pagination = $this->model->paginate($page);
		if (!($url = Router::getRoute($this->model->name.'Paged'))) {
			$url = Router::getRoute('scaffold_paged', array('controller' => $this->model->name));
		}
		$pagination['url'] = $url;
		$this->controller->data->set('pagination', $pagination);
		$this->controller->data->set(Inflector::plural($this->model->name), $entries);
		$this->controller->data->set('data', $entries);
		if (!$entries) {
			return true;
		}
		return $entries;
	}
	
	public function create()
	{
		if (isset($this->form) && isset($this->model) && $this->form->ok()) {
			$this->form->toModel($this->model);
			if ($this->model->save()) {
				if (isset($this->controller->FlashMessage)) {
					$this->controller->FlashMessage->set(__('Successfully created :1.', $this->model), FlashMessageType::SUCCESS);
				}
				return $this->model;
			} else {
				$this->form->errors = $model->validationErrors();
				return false;
			}
		}
		return true;
	}
	
	public function edit($id)
	{
		if (!($model = $this->view($id))) {
			return false;
		}
		if (isset($this->form)) {
			$this->form->fromModel($model);
			if ($this->form->ok()) {
				if ($this->form->toModel($model) && $model->save()) {
					if (isset($this->controller->FlashMessage)) {
						$this->controller->FlashMessage->set(__('Successfully saved changes.'), FlashMessageType::SUCCESS);
					}
					return $model;
				} else {
					$this->form->errors = $model->validationErrors();
					if (isset($this->controller->FlashMessage)) {
						$this->controller->FlashMessage->set(__('Error while saving changes in :1.', $model->name), FlashMessageType::ERROR);
					}
				}
			}
		}
		return $model;
	}
	
	public function view($id)
	{
		if (!($this->model = $this->model->findById($id))) {
			return false;
		}
		$this->controller->data->set($this->model->name, $this->model);
		$this->controller->data->set('data', $this->model);
		return $this->model;
	}
	
	public function delete($id)
	{
		if (!($model = $this->model->findById($id))) {
			return false;
		}
		$result = $model->delete();
		if (isset($this->controller->FlashMessage)) {
			if ($result) {
				$this->controller->FlashMessage->set(__('Successfully deleted :1.', $model), FlashMessageType::SUCCESS);
			} else {
				$this->controller->FlashMessage->set(__('Error while deleting :1.', $model), FlashMessageType::ERROR);
			}
		}
		return $this->controller->redirect(Router::uri('scaffold', array('controller' => $this->controller->name, 'action' => 'index')));
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exceptions
 */
class ScaffoldException extends ComponentException {}