<!DOCTYPE html>

<meta charset="utf-8">
<meta name="description" content="PHLAKNET Discord Bot">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">

<title>PHLAKNET Discord Bot</title>

<header>
    <h1>PHLAKNET Discord Bot</h1>
</header>

<main>
    <p align="center">
        <a href="https://github.com/PHLAK/discord-bot">https://github.com/PHLAK/discord-bot</a>
    </p>

    @auth
        <hr>

        <div align="center" style="margin-bottom: 1rem;">
            <a href="#">Pulse</a> &bull; <a href="{{ route('telescope') }}">Telescope</a>
        </div>

        <div style="display: flex; justify-content: center;">
            <form action="{{ route('logout') }}" method="post">
                @csrf
                <button type="submit" style="background-color: #000; border-color: #000; display: inline-flex; align-items: center; justify-content: center;">Sign out</button>
            </form>
        </div>
    @endauth
</main>

<footer>
    Made by <a href="https://www.chriskankiewicz.com">Chris Kankiewicz</a>
</footer>
