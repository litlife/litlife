<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		\Illuminate\Support\Facades\DB::transaction(function () {

			$forum = new \App\Forum();
			$forum->name = __('idea.idea_forum_title');
			$forum->description = __('idea.idea_forum_description');
			$forum->autofix_first_post_in_created_topics = true;
			$forum->order_topics_based_on_fix_post_likes = true;
			$forum->is_idea_forum = true;
			$forum->save();

			\App\Variable::updateOrCreate([
				'name' => \App\Enums\VariablesEnum::IdeaForum
			], ['value' => $forum->id]);

			$forum = new \App\Forum();
			$forum->name = __('Questions and answers');
			$forum->description = __('Asking, answering, looking for answers');
			$forum->autofix_first_post_in_created_topics = false;
			$forum->order_topics_based_on_fix_post_likes = false;
			$forum->is_idea_forum = false;
			$forum->save();

			\App\Variable::updateOrCreate([
				'name' => \App\Enums\VariablesEnum::ForumOfQuestions
			], ['value' => $forum->id]);
		});
	}
}
