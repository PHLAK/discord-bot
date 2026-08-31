<!DOCTYPE html>

<meta charset="utf-8">
<meta name="description" content="PHLAKNET Discord Bot">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">

<title>Sign in &bull; PHLAKNET Discord Bot</title>

<header>
    <h1>PHLAKNET Discord Bot</h1>
</header>

<main>
    @if ($errors->any())
        <blockquote>
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </blockquote>
    @endif

    <p align="center">
        <a class="button" href="{{ route('oauth.pocketid.redirect') }}" style="background-color: #000; border-color: #000; display: inline-flex; align-items: center; justify-content: center; gap: 0.5em;">
            <img src="https://cdn.jsdelivr.net/gh/selfhst/icons@main/webp/pocket-id-light.webp" alt="Pocket ID" style="height: 1.5rem;">
            Sign in with Pocket ID
        </a>
    </p>
</main>

<footer>
    Made by <a href="https://www.chriskankiewicz.com">Chris Kankiewicz</a>
</footer>
