saxulum
=======

What is saxulum?
----------------

Saxulum is a Symfony2 like advanced Silex skeleton, every saxulum provider runs on Silex itself.

Why saxulum?
------------

Symfony2 as a fullstack framework is a great work of each contributor. There are great documentations to use it.
As a new developer, inexperienced developer, or one never learned pattern like mvc it could be very hard to understand
how fullstack frameworks like Symfony2 work, those parts you can't learn with existing documentation, for example
the internals of the caching. On the other side, there is Silex, a great microframework, much easier to learn, but
without conventions, far away from a fullstack framework. Thats the reason, why i @dominikzogg started with saxulum,
for those who like conventions, but prefere something more easy to learn.

What makes saxulum beginner friendly?
-------------------------------------

Saxulum uses all built-in service providers from [Silex][1], which means that Saxulum preconfigure the use of [Twig][2]
with integration for the [Urlgenerator][3], the [Form][4] component and the [Translator][5]. Thirdparty Silex providers
as [Doctrine Registry][6] fully integrate the [Doctrine ORM][7]. So you can start with the orm without additional configuration.
With the [Assetic][8] integration, which searchs for stylesheet and javascript blocks within the templates makes it as easy as possible to use Saxulum.

Howto
-----

Create a new project: composer create-project saxulum/saxulum myproject --stability="dev"

[1] http://silex.sensiolabs.org/documentation
[2] http://silex.sensiolabs.org/doc/providers/twig.html
[3] http://silex.sensiolabs.org/doc/providers/url_generator.html
[4] http://silex.sensiolabs.org/doc/providers/form.html
[5] http://silex.sensiolabs.org/doc/providers/translation.html
[6] https://github.com/dominikzogg/doctrine-orm-manager-registry-provider
[7] http://www.doctrine-project.org/projects/orm.html
[8] https://github.com/kriswallsmith/assetic
