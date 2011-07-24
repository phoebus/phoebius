<?php





// sample definition of a form
class EditPhotoForm extends Form 
{
	function __construct(Photo $photo, HttpUrl $url)
	{
		parent::__construct(__CLASS__, $url);
		$this->addControl(FormControl::string("name"));
		$this->addControl(FormControl::email("email"));
		$this->addControl(FormControl::password("password"));
		$this->addControl(FormControl::string("website", false));
		$this->addControl(FormControl::textarea("about"));
		$this->addControl(FormControl::image("image"));
		$this->addControl(FormControl::file("metafile"));
		$this->addControl(FormControl::fileSet("images"));
		$this->addControl(FormControl::checkbox("is_private"));
		$this->addControl(FormControl::checkboxSet("licence", array(0 => 'free', 1 => 'pay'), array(0, 1)));
		$this->addControl(FormControl::select("quality", array(0 => 'low', 1 => 'normal', 2 => 'high'), 0));
		$this->addControl(FormControl::selectMultiple("license", array(0 => 'free', 1 => 'pay'), array(0, 1)));
		$this->addControl(FormControl::radioGroup("priority", array(0 => 'low', 1 => 'normal', 2 => 'high'), 0));
		
		$this->addButton("save", "Save", array($this, 'onSave'), false);
		$this->addButton("delete", "Delete", array($this, 'onDelete'), true);
	}
}

// sample usage
class UserController extends ArchbyUserController
{
	function action_photo(RouteData $routeData, WebRequest $request, $id = null) 
	{
		if ($id) {
			try {
				$photo = Photo::dao()->getEntityById($id);
			}
			catch (OrmEntityNotFoundException $e) {
				throw new DispatchException($routeData, $request);
			}
			
			if (!$photo->canEdit($this->getAuthorizedUser())) {
				throw new DispatchException($routeData, $request);
			}
			
			$form = new EditPhotoForm($photo, $request->getHttpUrl());
		}
		else {
			$form = new AddPhotoForm($request->getHttpUrl());
		}
		
		if ($form->handle($request)) {
			return new RedirectResult($routeData->getRouter()->getUrl("edit_photo", array('success' => 1)));
		}
		else {
			return new ViewResult(new View('user/photo', array('form' => $form)));
		}
	}
}

class LoginForm extends Form
{
	
}

class IndexController extends ArchbyController
{
	function action_login(WebRequest $request)
	{
		$form = new Loginform;
		if ($form->handle($request)) {
			return new RedirectResult('/');
		}
		else {
			
		}
	}
}


FormControl:
 - required/optional
 - error: missing, wrong


interface IFormControl {
	function isOptional();
	function markMissing();
	function markWrong($message = null);
}
 


// sign-less:
$form = new SomeForm();
$form->import($request->getPost());
if ($form->hasErrors()) {
	//auth failed
}
else {
	// all ok
}
return new View("auth", array('form' => $form));



// protected:
$form = new SomeForm(..., $signed = true);
try {
	$form->process($request);
	if (!$form->hasErrors())
		return "/";
}
catch (FormSubmitException $e) {
	// not submitted, not matched or invalidated	
	$form->setup();
}
return new View("auth", array('form' => $form));

