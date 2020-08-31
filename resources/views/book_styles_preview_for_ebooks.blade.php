<link href="{{ mix('css/styles_for_epub_books.css', config('litlife.assets_path')) }}" rel="stylesheet">

<body style="background-color:#FFF;">
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
		<s>{{ $faker->sentence(3) }}</s> <sub>{{ $faker->sentence(3) }}</sub> <sup>{{ $faker->sentence(3) }}</sup>

		<code>{{ $faker->sentence(3) }}</code>
	</p>

	<p>{{ $faker->sentence(30) }}</p>

	<blockquote>
		blockquote blockquote blockquote blockquote blockquote blockquote blockquote blockquote blockquote blockquote
		blockquote
		blockquote blockquote blockquote blockquote blockquote blockquote blockquote blockquote blockquote blockquote
		blockquote
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

</body>