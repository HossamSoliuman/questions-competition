<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar ">
    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a class="nav-link btn btn-secondary btn-block mb-2" href="{{ route('index') }}">
                Home
            </a>
        </li>


        <li class="nav-item">
            <a class="nav-link btn btn-secondary btn-block mb-2" href="{{ route('competitions.index') }}">
                Competitions
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link btn btn-secondary btn-block mb-2" href="{{ route('groups.index') }}">
                Groups
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link btn btn-secondary btn-block mb-2" href="{{ route('teams.index') }}">
                Teams
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link btn btn-secondary btn-block mb-2" href="{{ route('tests.index') }}">
                Tests
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link btn btn-secondary btn-block mb-2" href="{{ route('questions.index') }}">
                Questions
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link btn btn-secondary btn-block mb-2" href="{{ route('categories.index') }}">
                Categories
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link btn btn-secondary btn-block mb-2" href="{{ route('audiences.index') }}">
                Audiences
            </a>
        </li>


        <li class="nav-item">

            <form action="{{ route('logout') }}" method="post">
                @csrf
                <button type="submit" class="nav-link btn btn-danger btn-block mb-2">
                    Logout
                </button>
                </a>

            </form>
        </li>

    </ul>
</nav>
