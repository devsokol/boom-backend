<?php

namespace Modules\ApiV1\Http\Controllers;

use App\Models\Project;
use App\Transformers\ProjectTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use League\Fractal\Serializer\JsonApiSerializer;
use Modules\ApiV1\Actions\Project\ActiveProjectsRolesList;
use Modules\ApiV1\Actions\Project\ProjectCheckRights;
use Modules\ApiV1\Actions\Project\ProjectDelete;
use Modules\ApiV1\Actions\Project\ProjectList;
use Modules\ApiV1\Actions\Project\ProjectMakeArchive;
use Modules\ApiV1\Actions\Project\ProjectMakeRestore;
use Modules\ApiV1\Actions\Project\ProjectRelations;
use Modules\ApiV1\Actions\Project\ProjectStore;
use Modules\ApiV1\Actions\Project\ProjectUpdate;
use Modules\ApiV1\Http\Requests\ProjectRequest;
use Modules\ApiV1\Transformers\ProjectListTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class ProjectController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/projects",
     *   tags={"Project"},
     *   summary="Fetch projects",
     *   description="Fetch projects",
     *   operationId="fetchProjects",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="filterByStatus",
     *       in="query",
     *       description="type: active: 1, archived: 2",
     *       required=false
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Success",
     *       content={@OA\MediaType(mediaType="application/json",),}
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated."
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Not Found"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function index(Request $request, ProjectList $action): array
    {
        return Project::makeCacheByUniqueRequest(function () use ($request, $action) {
            $projects = $action->handle($request->all());

            return fractal($projects, new ProjectTransformer())
                ->withResourceName('project')
                ->serializeWith(new JsonApiSerializer());
        });
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/projects/{id}",
     *   tags={"Project"},
     *   summary="Fetch a project",
     *   description="Fetch a project by id",
     *   operationId="fetchProject",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="id",
     *       required=true,
     *       in="path",
     *       description="id",
     *       @OA\Schema(
     *           type="integer"
     *       )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Success",
     *       content={@OA\MediaType(mediaType="application/json",),}
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated."
     *   ),
     *   @OA\Response(
     *       response=403,
     *       description="This action is unauthorized."
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Not Found"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function show(Project $project): Fractal
    {
        (new ProjectCheckRights())->handle($project);
        (new ProjectRelations())->handle($project);

        return fractal($project, new ProjectTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/projects",
     *   tags={"Project"},
     *   summary="Create a new project",
     *   description="Create a new project",
     *   operationId="createProject",
     *   security={ {"bearerAuth": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               required={"name", "description", "start_date", "deadline", "genre_id", "user_id"},
     *               @OA\Property(property="name", type="string", description="project name", example="Project title"),
     *               @OA\Property(property="description", type="string", description="project description", example="Some text will be here..."),
     *               @OA\Property(property="placeholder", type="string", description="project image", example="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAApgAAAKYB3X3/OAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAANCSURBVEiJtZZPbBtFFMZ/M7ubXdtdb1xSFyeilBapySVU8h8OoFaooFSqiihIVIpQBKci6KEg9Q6H9kovIHoCIVQJJCKE1ENFjnAgcaSGC6rEnxBwA04Tx43t2FnvDAfjkNibxgHxnWb2e/u992bee7tCa00YFsffekFY+nUzFtjW0LrvjRXrCDIAaPLlW0nHL0SsZtVoaF98mLrx3pdhOqLtYPHChahZcYYO7KvPFxvRl5XPp1sN3adWiD1ZAqD6XYK1b/dvE5IWryTt2udLFedwc1+9kLp+vbbpoDh+6TklxBeAi9TL0taeWpdmZzQDry0AcO+jQ12RyohqqoYoo8RDwJrU+qXkjWtfi8Xxt58BdQuwQs9qC/afLwCw8tnQbqYAPsgxE1S6F3EAIXux2oQFKm0ihMsOF71dHYx+f3NND68ghCu1YIoePPQN1pGRABkJ6Bus96CutRZMydTl+TvuiRW1m3n0eDl0vRPcEysqdXn+jsQPsrHMquGeXEaY4Yk4wxWcY5V/9scqOMOVUFthatyTy8QyqwZ+kDURKoMWxNKr2EeqVKcTNOajqKoBgOE28U4tdQl5p5bwCw7BWquaZSzAPlwjlithJtp3pTImSqQRrb2Z8PHGigD4RZuNX6JYj6wj7O4TFLbCO/Mn/m8R+h6rYSUb3ekokRY6f/YukArN979jcW+V/S8g0eT/N3VN3kTqWbQ428m9/8k0P/1aIhF36PccEl6EhOcAUCrXKZXXWS3XKd2vc/TRBG9O5ELC17MmWubD2nKhUKZa26Ba2+D3P+4/MNCFwg59oWVeYhkzgN/JDR8deKBoD7Y+ljEjGZ0sosXVTvbc6RHirr2reNy1OXd6pJsQ+gqjk8VWFYmHrwBzW/n+uMPFiRwHB2I7ih8ciHFxIkd/3Omk5tCDV1t+2nNu5sxxpDFNx+huNhVT3/zMDz8usXC3ddaHBj1GHj/As08fwTS7Kt1HBTmyN29vdwAw+/wbwLVOJ3uAD1wi/dUH7Qei66PfyuRj4Ik9is+hglfbkbfR3cnZm7chlUWLdwmprtCohX4HUtlOcQjLYCu+fzGJH2QRKvP3UNz8bWk1qMxjGTOMThZ3kvgLI5AzFfo379UAAAAASUVORK5CYII="),
     *               @OA\Property(property="start_date", type="string", description="start date", example="2022-08-15"),
     *               @OA\Property(property="deadline", type="string", description="deadline", example="2022-09-15"),
     *               @OA\Property(property="genre_id", type="string", description="Genre ID", example="1"),
     *               @OA\Property(property="project_type_id", type="string", description="Project type ID", example="1"),
     *           )
     *       )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Success",
     *     @OA\JsonContent()
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated."
     *   ),
     *   @OA\Response(
     *       response=403,
     *       description="This action is unauthorized."
     *   ),
     *   @OA\Response(
     *       response=422,
     *       description="Unprocessable Entity"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function store(ProjectRequest $request): JsonResponse
    {
        $project = (new ProjectStore())->handle($request->validated());

        (new ProjectRelations())->handle($project);

        return fractal($project, new ProjectTransformer())->serializeWith(new ArraySerializer())->respond(201, []);
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Put(path="/projects/{id}",
     *   tags={"Project"},
     *   summary="Update a project",
     *   description="Update a project",
     *   operationId="updateProject",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="id",
     *       required=true,
     *       in="path",
     *       description="id",
     *       @OA\Schema(
     *           type="integer"
     *       )
     *   ),
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               required={"name", "description", "start_date", "deadline", "genre_id", "user_id"},
     *               @OA\Property(property="name", type="string", description="project name", example="Project title"),
     *               @OA\Property(property="description", type="string", description="project description", example="Some text will be here..."),
     *               @OA\Property(property="placeholder", type="string", description="project image", example="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAApgAAAKYB3X3/OAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAANCSURBVEiJtZZPbBtFFMZ/M7ubXdtdb1xSFyeilBapySVU8h8OoFaooFSqiihIVIpQBKci6KEg9Q6H9kovIHoCIVQJJCKE1ENFjnAgcaSGC6rEnxBwA04Tx43t2FnvDAfjkNibxgHxnWb2e/u992bee7tCa00YFsffekFY+nUzFtjW0LrvjRXrCDIAaPLlW0nHL0SsZtVoaF98mLrx3pdhOqLtYPHChahZcYYO7KvPFxvRl5XPp1sN3adWiD1ZAqD6XYK1b/dvE5IWryTt2udLFedwc1+9kLp+vbbpoDh+6TklxBeAi9TL0taeWpdmZzQDry0AcO+jQ12RyohqqoYoo8RDwJrU+qXkjWtfi8Xxt58BdQuwQs9qC/afLwCw8tnQbqYAPsgxE1S6F3EAIXux2oQFKm0ihMsOF71dHYx+f3NND68ghCu1YIoePPQN1pGRABkJ6Bus96CutRZMydTl+TvuiRW1m3n0eDl0vRPcEysqdXn+jsQPsrHMquGeXEaY4Yk4wxWcY5V/9scqOMOVUFthatyTy8QyqwZ+kDURKoMWxNKr2EeqVKcTNOajqKoBgOE28U4tdQl5p5bwCw7BWquaZSzAPlwjlithJtp3pTImSqQRrb2Z8PHGigD4RZuNX6JYj6wj7O4TFLbCO/Mn/m8R+h6rYSUb3ekokRY6f/YukArN979jcW+V/S8g0eT/N3VN3kTqWbQ428m9/8k0P/1aIhF36PccEl6EhOcAUCrXKZXXWS3XKd2vc/TRBG9O5ELC17MmWubD2nKhUKZa26Ba2+D3P+4/MNCFwg59oWVeYhkzgN/JDR8deKBoD7Y+ljEjGZ0sosXVTvbc6RHirr2reNy1OXd6pJsQ+gqjk8VWFYmHrwBzW/n+uMPFiRwHB2I7ih8ciHFxIkd/3Omk5tCDV1t+2nNu5sxxpDFNx+huNhVT3/zMDz8usXC3ddaHBj1GHj/As08fwTS7Kt1HBTmyN29vdwAw+/wbwLVOJ3uAD1wi/dUH7Qei66PfyuRj4Ik9is+hglfbkbfR3cnZm7chlUWLdwmprtCohX4HUtlOcQjLYCu+fzGJH2QRKvP3UNz8bWk1qMxjGTOMThZ3kvgLI5AzFfo379UAAAAASUVORK5CYII="),
     *               @OA\Property(property="start_date", type="string", description="start date", example="2022-08-15"),
     *               @OA\Property(property="deadline", type="string", description="deadline", example="2022-09-15"),
     *               @OA\Property(property="genre_id", type="string", description="Genre ID", example="1"),
     *               @OA\Property(property="project_type_id", type="string", description="Project type ID", example="1"),
     *           )
     *       )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent()
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated."
     *   ),
     *   @OA\Response(
     *       response=403,
     *       description="This action is unauthorized."
     *   ),
     *   @OA\Response(
     *       response=422,
     *       description="Unprocessable Entity"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function update(ProjectRequest $request, Project $project): Fractal
    {
        (new ProjectCheckRights())->handle($project);
        (new ProjectUpdate())->handle($project, $request->validated());
        (new ProjectRelations())->handle($project);

        return fractal($project, new ProjectTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Delete(path="/projects/{id}",
     *   tags={"Project"},
     *   summary="Delete a project",
     *   description="Delete a project",
     *   operationId="deleteProject",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     required=true,
     *     in="path",
     *     description="id",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=204,
     *     description="Success",
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated. / This action is unauthorized."
     *   ),
     *   @OA\Response(response=403, description="This action is unauthorized."),
     *   @OA\Response(response=404, description="Not found")
     * )
     * @codingStandardsIgnoreEnd
     */
    public function destroy(Project $project): JsonResponse
    {
        (new ProjectCheckRights())->handle($project);
        (new ProjectDelete())->handle($project);

        return response()->json([], 204);
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/projects/{id}/archive",
     *   tags={"Project"},
     *   summary="Archive a project",
     *   description="Archive a project",
     *   operationId="archiveProject",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     required=true,
     *     in="path",
     *     description="id",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated. / This action is unauthorized."
     *   ),
     *   @OA\Response(response=403, description="This action is unauthorized."),
     *   @OA\Response(response=404, description="Not found")
     * )
     * @codingStandardsIgnoreEnd
     */
    public function archive(Project $project): Fractal
    {
        (new ProjectCheckRights())->handle($project);
        (new ProjectMakeArchive())->handle($project);
        (new ProjectRelations())->handle($project);

        return fractal($project, new ProjectTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/projects/{id}/restore",
     *   tags={"Project"},
     *   summary="Restore a project",
     *   description="Restore a project",
     *   operationId="restoreProject",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     required=true,
     *     in="path",
     *     description="id",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated. / This action is unauthorized."
     *   ),
     *   @OA\Response(response=403, description="This action is unauthorized."),
     *   @OA\Response(response=404, description="Not found")
     * )
     * @codingStandardsIgnoreEnd
     */
    public function restore(Project $project): Fractal
    {
        (new ProjectCheckRights())->handle($project);
        (new ProjectMakeRestore())->handle($project);
        (new ProjectRelations())->handle($project);

        return fractal($project, new ProjectTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/projects/active-projects-and-roles",
     *   tags={"Project"},
     *   summary="Fetch active projects with active roles",
     *   description="Fetch active projects with active roles",
     *   operationId="fetchActiveProjectsRoles",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Response(
     *       response=200,
     *       description="Success",
     *       content={@OA\MediaType(mediaType="application/json",),}
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated."
     *   ),
     *   @OA\Response(
     *       response=403,
     *       description="This action is unauthorized."
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Not Found"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function activeProjectsRoles(ActiveProjectsRolesList $action): Fractal
    {
        $projects = $action->handle();

        return fractal($projects, new ProjectListTransformer())->serializeWith(new ArraySerializer());
    }
}
