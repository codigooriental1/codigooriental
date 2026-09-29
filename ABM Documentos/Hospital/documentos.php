<?php
require_once "conexion.php";

$sql = "SELECT * FROM documento WHERE activo = 1 ORDER BY fecha_emision DESC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>S.I.G.S.M. | Gestión de Documentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listado de Documentos Médicos</h2>
        <a href="alta_documento.php" class="btn btn-primary">+ Nuevo Documento</a>
    </div>

    <?php if (isset($_GET['ok'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php
                if ($_GET['ok'] == 1) echo "Documento creado correctamente.";
                elseif ($_GET['ok'] == 2) echo "Documento actualizado correctamente.";
                elseif ($_GET['ok'] == 3) echo "Documento eliminado correctamente.";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Cédula Paciente</th>
                        <th>Fecha Emisión</th>
                        <th>Ruta del Archivo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && $resultado->num_rows > 0): ?>
                        <?php while ($row = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                                <td>
                                    <?php echo $row['tipo'] === 'indicacion' ? 'Indicación médica' : 'Información general'; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['cedula_paciente']); ?></td>
                                <td><?php echo date("d/m/Y", strtotime($row['fecha_emision'])); ?></td>
                                <td><code><?php echo htmlspecialchars($row['ruta_archivo']); ?></code></td>
                                <td>
                                    <a href="editar_documento.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                    
                                    <form action="baja_documento.php" method="POST" class="d-inline" onsubmit="return confirm('¿Confirma dar de baja este documento?');">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Baja</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-3">No hay documentos registrados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>