## Connecting a repository

In order to connect a repository with blog articles, you have to link it via `app:add-blog`. You also have to add a
commit hook in your repository with the secret provided. This makes sure that both the initial creation and additional
updates in the repository will sync it to the app.


### Repository Layout

The main file you need is the `blog.yml`, this contains the settings of your blog. An example below:

```yml
author:
    name:  Henk de Vries
    email: henk@devries.com
    urls:
        github:   https://.../
        facebook: https://.../
        twitter:  https://.../

settings:
    introduction: author.md
    drafts: /drafts/
    published: /published/
```

Additional settings you can apply:
 - `introduction`: links to your intro which will be displayed on the right side next to posts.
 - `drafts`: which indicates your draft blog posts directory.
 - `published`: which indicates your published blog posts directory.


### Blog Post Statuses

There are three different statuses a blog post can have:
 - Draft: will be displayed when the url is accessed directly but not listed. Disqus features are disabled for drafts.
 - Unpublished: same as draft but in the published directory and have a date in the future. Once the date has been
   passed they will become published.
 - Published: when the publish date has been passed in the published directory.


### Blog Post Metadata

Blog posts support metadata. Currently supported metadata:
 - `NAME`: The title and name of your blog.
 - `DATE`: The publish date, once passed it will be shown in listings. This only applies on posts in the published
   directory. The recommended format is [ISO 8601][iso wikipedia].
 - `TAGS`: A list of comma separated tags added to the post. They will be shown below the post and you can filter on
   tags.

Metadata can be annotated in Markdown Comments and should be placed at the start of the file:
```md
[//]: # (TITLE: An Example Blog Post)
[//]: # (DATE: 2016-07-03T10:00:00+01:00)
[//]: # (TAGS: example, md, some foo bar)
```

[iso wikipedia]:https://en.wikipedia.org/wiki/ISO_8601
