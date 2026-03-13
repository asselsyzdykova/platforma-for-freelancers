<!DOCTYPE html>
<html>

<body>
    <h2>Hello! A new project matches your skills.</h2>
    <p><strong>Project:</strong> {{ $project->title }}</p>
    <p><strong>Category:</strong> {{ $project->category }}</p>
    <p><strong>Budget:</strong> {{ $project->budget }}€</p>
    <br>
    <a href="{{ rtrim(env('FRONTEND_URL'), '/') }}/projects/{{ $project->id }}"
        style="background: #27ae60; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
        View Project Details
    </a>
</body>

</html>
