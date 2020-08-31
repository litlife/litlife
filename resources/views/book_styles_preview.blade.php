@extends('layouts.app')

@section('content')

	@include('read_style_css')

	<div class="card">
		<div class="card-body book_text" style="font-size: 20px">

			<div class="u-annotation">
				<p>Аннотация {{ $faker->text(1000) }}</p>
			</div>

			<div class="u-section">

				<div class="u-empty-line"></div>

				<div class="u-poem">
					<div class="u-title">{{ $faker->sentence(3) }}</div>
					<div class="u-epigraph"><p>{{ $faker->sentence(3) }}</p></div>
					<div class="u-stanza">
						<p class="u-v">{{ $faker->sentence(3) }}</p>
						<p class="u-v">{{ $faker->sentence(3) }}</p>
						<p class="u-v">{{ $faker->sentence(3) }}</p>
					</div>
					<div class="u-stanza">
						<div class="u-title">Stanza title</div>
						<p class="u-v">{{ $faker->sentence(3) }}</p>
						<p class="u-v">{{ $faker->sentence(3) }}</p>
						<p class="u-v">{{ $faker->sentence(3) }}</p>
					</div>
					<div class="u-text-author">{{ $faker->firstName }} {{ $faker->lastName }}</div>
					<div class="u-date">{{ $faker->date() }}</div>
				</div>

				<div class="u-subtitle">* * *</div>

				<div class="u-epigraph">
					<p>{{ $faker->sentence(3) }}</p>
					<blockquote>
						{{ $faker->sentence(3) }}
					</blockquote>
					<p>{{ $faker->sentence(3) }}</p>
					<div class="u-text-author">
						{{ $faker->firstName }} {{ $faker->lastName }}
					</div>
				</div>

				<p>{{ $faker->sentence(30) }}</p>

				<blockquote>
					<p>{{ $faker->sentence(3) }}</p>
					<div class="u-empty-line"></div>
					<p>{{ $faker->sentence(3) }}</p>
					<div class="u-empty-line"></div>
					<table>
						<tr>
							<td>{{ $faker->lastName }} {{ $faker->firstName }}</td>
							<td>{{ $faker->phoneNumber }}</td>
						</tr>
						<tr>
							<td>{{ $faker->lastName }} {{ $faker->firstName }}</td>
							<td>{{ $faker->phoneNumber }}</td>
						</tr>
					</table>
					<div class="u-text-author">
						{{ $faker->firstName }} {{ $faker->lastName }}
					</div>
				</blockquote>

				<p>
					<b>{{ $faker->sentence(3) }}</b> <i>{{ $faker->sentence(3) }}</i>
					<a href="/" data-type="section">section</a>
					<a href="/" data-type="note">note</a>
					<s>{{ $faker->sentence(3) }}</s> <sub>{{ $faker->sentence(3) }}</sub>
					<sup>{{ $faker->sentence(3) }}</sup>

					<code>{{ $faker->sentence(3) }}</code>
				</p>

				<p>{{ $faker->sentence(30) }}</p>

				<blockquote>
					blockquote blockquote blockquote blockquote blockquote blockquote blockquote blockquote blockquote
					blockquote blockquote
					blockquote blockquote blockquote blockquote blockquote blockquote blockquote blockquote blockquote
					blockquote blockquote
					blockquote blockquote
				</blockquote>

				<ol>
					<li>{{ $faker->sentence(3) }}</li>
					<li>{{ $faker->sentence(3) }}</li>
				</ol>

				<ul>
					<li>{{ $faker->sentence(3) }}</li>
					<li>{{ $faker->sentence(3) }}</li>
				</ul>

				<dl>
					<dd>{{ $faker->sentence(3) }}</dd>
					<dd>{{ $faker->sentence(3) }}</dd>
				</dl>

				<div><img alt="logo.svg" class="u-image-align-right" src="https://litlife.club/img/logo.svg"/></div>
				<div><img alt="logo.svg" class="u-image-align-left" src="https://litlife.club/img/logo.svg"/></div>
				<div class="u-image-align-center"><img alt="logo.svg" src="https://litlife.club/img/logo.svg"/></div>


			</div>

		</div>
	</div>

	{{--

			 $a['section'] = array('f' => 'section');
			$a['title'] = array('s' => '<div class="fb2-title">', 'e' => '</div>');
			$a['epigraph'] = array('s' => '<div class="fb2-epigraph">', 'e' => '</div>');
			$a['a'] = array('f' => 'a', 'text' => '1');
			$a['image'] = array('f' => 'image');
			$a['sup'] = array('s' => '<sup>', 'e' => '</sup>', 'text' => '1');
			$a['sub'] = array('s' => '<sub>', 'e' => '</sub>', 'text' => '1');
			$a['strikethrough'] = array('s' => '<s>', 'e' => '</s>', 'text' => '1');
			$a['style'] = array('s' => '<p>', 'e' => '</p>', 'text' => '1');
			$a['emphasis'] = array('s' => '<i>', 'e' => '</i>', 'text' => '1');
			$a['strong'] = array('s' => '<b>', 'e' => '</b>', 'text' => '1');
			$a['p'] = array('s' => '<p>', 'e' => '</p>', 'text' => '1');
			$a['epigraph'] = array('s' => '<div class="fb2-epigraph">', 'e' => '</div>');
			$a['text-author'] = array('s' => '<div class="fb2-text-author">', 'e' => '</div>', 'text' => '1');
			$a['date'] = array('s' => '', 'e' => '');
			$a['annotation'] = array('s' => '<div class="fb2-annotation">', 'e' => '</div>');
			$a['poem'] = array('s' => '<div class="fb2-poem">', 'e' => '</div>');
			$a['stanza'] = array('s' => '<div class="fb2-stanza">', 'e' => '</div>');
			$a['subtitle'] = array('s' => '<div class="fb2-subtitle">', 'e' => '</div>', 'text' => '1');
			$a['empty-line'] = array('s' => '<div class="fb2-empty-line">', 'e' => '</div>');
			$a['cite'] = array('s' => '<div class="fb2-cite">', 'e' => '</div>');
			$a['table'] = array('s' => '<table class="lts49">', 'e' => '</table>');
			$a['tr'] = array('s' => '<tr>', 'e' => '</tr>');
			$a['th'] = array('f' => 'th');
			$a['td'] = array('f' => 'td');
			$a['v'] = array('s' => '<div class="fb2-v">', 'e' => '</div>', 'text' => '1');
			$a['code'] = array('f' => 'code', 'text' => '1');
	--}}

	<div class="card mt-2">
		<div class="card-body book_text imgs-fluid" style="font-size: 20px">
			<div class="page_text">
				<a name="section_1" class="fb2-title-main">
					<p>{{ $faker->sentence(2) }}</p>
					<p>{{ $faker->sentence(4) }}</p>
				</a>
				<div class="fb2-epigraph"><p>Epigraph epigraph epigraph epigraph</p></div>
				<div class="fb2-empty-line"></div>
				<a name="section_2" class="fb2-title"><p>{{ $faker->sentence(2) }}</p></a>
				<p>{{ $faker->sentence(3) }}</p>
				<p> — {{ $faker->sentence(7) }}<a name="read_n_1_back" href="#read_n_1"
												  class="note">[1]</a> {{ $faker->sentence(10) }}</p>
				<div class="fb2-subtitle">Subtitle subtitle subtitle subtitle</div>
				<div class="fb2-annotation">Annotation annotation annotation annotation annotation</div>
				<div class="fb2-cite">
					<p>Cite cite cite cite cite cite cite cite</p>
					<div class="fb2-empty-line"></div>
					<table>
						<tr>
							<td>{{ $faker->lastName }} {{ $faker->firstName }}</td>
							<td>{{ $faker->phoneNumber }}</td>
						</tr>
						<tr>
							<td>{{ $faker->lastName }} {{ $faker->firstName }}</td>
							<td>{{ $faker->phoneNumber }}</td>
						</tr>
					</table>
					<div class="fb2-text-author">
						Text author
					</div>
				</div>

				<div class="fb2-poem">
					<div class="fb2-title">Title title title title title title</div>
					<div class="fb2-epigraph"><p>Epigraph epigraph epigraph epigraph epigraph</p></div>
					<div class="fb2-stanza">
						<p class="fb2-v">V {{ $faker->sentence(3) }}</p>
						<p class="fb2-v">V {{ $faker->sentence(3) }}</p>
						<p class="fb2-v">V {{ $faker->sentence(3) }}</p>
					</div>
					<div class="fb2-stanza">
						<div class="fb2-title">Stanza title</div>
						<p class="fb2-v">V {{ $faker->sentence(3) }}</p>
						<p class="fb2-v">V {{ $faker->sentence(3) }}</p>
						<p class="fb2-v">V {{ $faker->sentence(3) }}</p>
					</div>
					<div class="fb2-text-author">Text author text author</div>
					<div class="fb2-date">Date date date</div>
				</div>

				<div class="fb2-subtitle">* * *</div>

				<div class="fb2-epigraph">
					<p>Epigraph epigraph epigraph epigraph</p>
					<div class="fb2-cite">
						Cite cite cite cite cite cite cite cite
					</div>
					<p>Epigraph epigraph epigraph epigraph</p>
					<div class="fb2-text-author">
						Text author text author text author text author text author text author text author
					</div>
				</div>

				<p>
					<b>{{ $faker->sentence(3) }}</b> <i>{{ $faker->sentence(3) }}</i> <a
							href="/">{{ $faker->sentence(2) }}</a>
					<s>{{ $faker->sentence(3) }}</s> <sub>{{ $faker->sentence(3) }}</sub>
					<sup>{{ $faker->sentence(3) }}</sup>

					<code>{{ $faker->sentence(3) }}</code>
				</p>
			</div>
			<div class="page_divided_line"></div>
			<div class="page_notes">
				<div class="fb2-section-note"><a class="fb2-note-back" name="read_n_1" href="#read_n_1_back">вернуться</a>
					<div id="read_n_1" class="fb2-section-notes">
						<div class="fb2-title"><p>1</p></div>
						<div class="fb2-empty-line"></div>
						<div class="fb2-image"><img alt="logo.svg" src="https://litlife.club/img/logo.svg"></div>
						<div class="fb2-empty-line"></div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<iframe src="{{ route('preview.book_styles_for_epub') }}" style="width:100%; height:600px;"></iframe>

@endsection