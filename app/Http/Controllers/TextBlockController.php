<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTextBlock;
use App\TextBlock;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TextBlockController extends Controller
{
	/**
	 * Приветствие сайта
	 *
	 * @param
	 * @return View
	 */
	public function welcome()
	{
		//$textBlock = TextBlock::where('name', 'Приветствие')->first();
		return view('text_block.welcome');
	}

	/**
	 * Приветствие сайта
	 *
	 * @param
	 * @return View
	 */
	public function keywordsHelper()
	{
		//$textBlock = TextBlock::where('name', 'Приветствие')->first();
		return view('text_block.keywords_helper');
	}

	/**
	 * Правила для платных книг
	 *
	 * @param
	 * @return View
	 */
	public function paidBookPublishingRules()
	{
		//$textBlock = TextBlock::where('name', 'Приветствие')->first();
		return view('text_block.paid_book_publishing_rules');
	}

	/**
	 * Соглашение об обработке персональных данных
	 *
	 * @param
	 * @return View
	 */
	public function personalDataProcessingAgreement()
	{
		//$textBlock = TextBlock::where('name', 'Приветствие')->first();
		return view('text_block.personal_data_processing_agreement');
	}

	/**
	 * Соглашение об обработке персональных данных
	 *
	 * @param
	 * @return View
	 */
	public function rules()
	{
		return view('text_block.rules');
	}

	function forRightsOwners()
	{
		return view('text_block.for_rights_owners');
	}

	function purchaseRules()
	{
		return view('text_block.purchase_rules');
	}

	function salesRules()
	{
		return view('text_block.sales_rules');
	}

	public function rulesPublishBooks()
	{
		return view('text_block.rules_publish_books');
	}

	/**
	 * Форма создания текстового блока
	 *
	 * @param Request $request
	 * @param $name
	 * @return View
	 * @throws
	 */
	public function create(Request $request, $name)
	{
		$this->authorize('create', TextBlock::class);

		return view('text_block.create', ['name' => $name]);
	}

	/**
	 * Сохранение
	 *
	 * @param StoreTextBlock $request
	 * @param $name string
	 * @return Response
	 * @throws
	 */
	public function store(StoreTextBlock $request, $name)
	{
		$this->authorize('create', TextBlock::class);

		$textBlock = new TextBlock($request->all());
		$textBlock->name = $name;
		$textBlock->user_edited_at = now();
		$textBlock->save();

		return redirect()
			->route('text_blocks.show', ['name' => $textBlock->name, 'id' => $textBlock->id]);
	}

	/**
	 *
	 * @param $name string
	 * @param $id int
	 * @return View
	 */
	public function show($name, $id)
	{
		$textBlock = TextBlock::where('name', $name)
			->when($id, function ($query) use ($id) {
				$query->where('id', $id);
			})
			->firstOrFail();

		return view('text_block.show', ['textBlock' => $textBlock]);
	}

	/**
	 *
	 * @param $name string
	 * @param $id int
	 * @return View
	 */
	public function versions($name)
	{
		$versions = TextBlock::where('name', $name ?? '')
			->get();

		if ($versions->count() < 1)
			abort(404);

		$this->authorize('update', $versions->first());

		return view('text_block.version.index', ['versions' => $versions]);
	}

	/**
	 *
	 * @param $name string
	 * @return View
	 */
	public function showLatestVersionForName($name)
	{
		$textBlock = TextBlock::latestVersion($name);

		return view('text_block.show', ['textBlock' => $textBlock]);
	}

	/**
	 * Форма редактирования блока
	 *
	 * @param  $name
	 * @return View
	 * @throws
	 */
	public function edit($name)
	{
		$textBlock = TextBlock::latestVersion($name);

		$this->authorize('update', $textBlock);

		return view('text_block.edit', compact('textBlock'));
	}

	/**
	 * Сохранение отредактированного
	 *
	 * @param StoreTextBlock $request
	 * @param  $name
	 * @return Response
	 * @throws
	 */
	public function update(StoreTextBlock $request, $name)
	{
		$textBlock = TextBlock::latestVersion($name);

		$this->authorize('update', $textBlock);

		$textBlockNew = new TextBlock($request->all());
		$textBlockNew->name = $textBlock->name;
		$textBlockNew->user_edited_at = now();
		$textBlockNew->save();

		return redirect()
			->route('text_blocks.show', ['name' => $textBlockNew->name, 'id' => $textBlockNew->id]);
	}
}
