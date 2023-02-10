<!DOCTYPE html>
<html lang="nl">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">


    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Jarno van Zelst Portofolio</title>

    <link rel="icon" href="img/favicon.png">

    <link href="css/portofolio/bootstrap.css" rel="stylesheet">
    <link href="css/portofolio/font-awesome.css" rel="stylesheet">
    <link href="css/portofolio/devicon.css" rel="stylesheet">
    <link href="css/portofolio/resume.css" rel="stylesheet">


    <style></style>
</head>

<body id="page-top">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top" id="sideNav">
        <a class="navbar-brand js-scroll-trigger" href="#page-top">
            <span class="d-block d-lg-none">Portofolio</span>
            <span class="d-none d-lg-block">
                <img class="img-fluid img-profile rounded-circle mx-auto mb-2" src="" alt="">
            </span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav">`
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger active" href="#about">Over mij</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="#experience">Ervaring</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="#education">Opleiding</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="#skills">Vaardigheden</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid p-0" style="margin-left: 3.5em">

        <section class="resume-section p-3 p-lg-5 d-flex d-column" id="about">
            <div class="my-auto">
                <h1 class="mb-0">{{ $aboutMe['name']['firstName'] }}
                    <span class="text-primary">{{ $aboutMe['name']['lastName'] }}</span>
                </h1>
                <div class="subheading mb-5">
                    {{ $aboutMe['address']['street'] .
                        ' · ' .
                        $aboutMe['address']['city'] .
                        ', ' .
                        $aboutMe['address']['zipcode'] .
                        ' · ' .
                        $aboutMe['phoneNumber'] .
                        ' ·' }}
                    <a href="mailto:name@email.com">{{ $aboutMe['email'] }}</a>
                </div>
                <p class="mb-5 regText">{{ $aboutMe['introduction'] }}</p>
                <ul class="list-inline list-social-icons mb-0">
                    <li class="list-inline-item">
                        <a href="{{ $aboutMe['linkedIn'] }}">
                            <span class="fa-stack fa-lg">
                                <i class="fa fa-circle fa-stack-2x"></i>
                                <i class="fa fa-linkedin fa-stack-1x fa-inverse"></i>
                            </span>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a href="{{ $aboutMe['gitHub'] }}">
                            <span class="fa-stack fa-lg">
                                <i class="fa fa-circle fa-stack-2x"></i>
                                <i class="fa fa-github fa-stack-1x fa-inverse"></i>
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </section>

        <section class="resume-section p-3 p-lg-5 d-flex flex-column" id="experience">
            <div class="my-auto">
                <h2 class="mb-5">Werk ervaring</h2>

                @foreach ($experiences as $experience)
                    <div class="resume-item d-flex flex-column flex-md-row mb-5 experienceContainer">
                        <div class="resume-content mr-auto">
                            <div class="resume-date">
                                <span class="text-sub"><i>{{ $experience['dateFrom'] }}</i> -
                                    <i>{{ $experience['dateTo'] }}</i>
                                </span>
                            </div>
                            <h3 class="mb-0">{{ $experience['title'] }}</h3>
                            <div class="subheading mb-3">{{ $experience['company'] }}</div>
                            <p>{{ $experience['description'] }}</p>
                        </div>

                    </div>
                @endforeach

        </section>

        <section class="resume-section p-3 p-lg-5 d-flex flex-column" id="education">
            <div class="my-auto">
                <h2 class="mb-5">Educatie</h2>
                @foreach ($educations as $education)
                    <div class="resume-item d-flex flex-column flex-md-row educationContainer">
                        <div class="resume-content mr-auto">
                            <div class="resume-date">
                                <span class="text-sub"><i>{{ $education['dateFrom'] }} - {{ $education['dateTo'] }}</i></span>
                            </div>
                            <h3 class="mb-0">{{ $education['school'] }}</h3>
                            <div class="subheading mb-3">{{ $education['type'] }}, {{ $education['level'] }}</div>
                        </div>

                    </div>
                @endforeach
            </div>
        </section>

        <section class="resume-section p-3 p-lg-5 d-flex flex-column" id="skills">
            <div class="my-auto">
                <h2 class="mb-5">Vaardigheden</h2>

                <div class="subheading mb-3">Programmeer talen, Frameworks &amp; Tools</div>
                <ul class="list-inline list-icons">
                    @foreach ($skills as $skill)
                        <li class="list-inline-item">
                            <i class="devicons devicon-{{ $skill['devicon'] }}"></i>
                        </li>
                    @endforeach
                </ul>

                <div class="subheading mb-3">Eigenschappen</div>
                <ul class="fa-ul mb-0">
                    @foreach ($traits as $trait)
                        <li>
                            <i class="fa-li fa fa-check"></i>
                            {{ $trait['trait'] }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    </div>

    <script src="js/portofolio/jquery.js"></script>
    <script src="js/portofolio/bootstrap.bundle.js"></script>
    <script src="js/portofolio/jquery.easing.js"></script>
    <script src="js/portofolio/resume.js"></script>

</body>

</html>
