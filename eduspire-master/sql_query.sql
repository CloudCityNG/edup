//get list of enrolles

ELECT id, userName, firstName, lastName, accessLevel, memberships
FROM  `users` 
WHERE 1 
AND (
 `memberships` LIKE  '%,1%'
OR  `memberships` LIKE  '%1,%'
OR  `memberships` LIKE  '%,1,%'
OR  `memberships` =1
)
AND accessLevel =10
LIMIT 100



SELECT  `cs`.`csID` ,  `cs`.`csGenreId` ,  `cs`.`csCourseType` ,  `cs`.`csCourseDefinitionId` ,  `cs`.`csRegistrationStartDate` ,  `cs`.`csRegistrationEndDate` ,  `cs`.`csPaymentStartDate` ,  `cs`.`csPublish` ,  `cd`.`cdID` ,  `cd`.`cdCourseID` ,  `cd`.`cdCourseTitle` ,
(SELECT COUNT( * ) 
FROM course_reservations
WHERE urCourse = cs.csID
) AS registered_count
,
(SELECT COUNT( * ) 
FROM users
WHERE 1
AND (
 `memberships` LIKE  CONCAT('%,',cs.csID,'%')
OR  `memberships` LIKE  CONCAT('%',cs.csID,',%')
OR  `memberships` LIKE  CONCAT('%,',cs.csID,',%')
OR  `memberships` LIKE   cs.csID
)
AND accessLevel =10
) AS enrollee_count

FROM (
 `course_schedule` cs
)
LEFT JOIN  `course_genres` cg ON  `cg`.`cgID` =  `cs`.`csGenreId` 
LEFT JOIN  `course_definitions` cd ON  `cg`.`cgID` =  `cd`.`cdGenre` 
LIMIT 10



SELECT cs.*, cd.cdID, cd.cdCourseID, cd.cdCourseTitle, (

SELECT COUNT( 1 ) 
FROM course_reservations
WHERE urCourse = cs.csID
) AS registered_count, (

SELECT COUNT( * ) 
FROM users
WHERE 1 
AND (
memberships LIKE CONCAT(  '%, ', cs.csID,  '%' ) 
OR memberships LIKE CONCAT(  '%', cs.csID,  ', %' ) 
OR memberships LIKE CONCAT(  '%, ', cs.csID,  ', %' ) 
OR memberships LIKE cs.csID
)
AND accessLevel =10
) AS enrollee_count
FROM (
`course_schedule` cs
)

LEFT JOIN  `course_genres` cg ON  `cg`.`cgID` =  `cs`.`csGenreId` 
LEFT JOIN  `course_definitions` cd ON  `cs`.`csCourseDefinitionId` =  `cd`.`cdID` 

//count records base 
SELECT cs.*, cd.cdID, cd.cdCourseID, cd.cdCourseTitle, (

SELECT COUNT( 1 ) 
FROM course_reservations
WHERE urCourse = cs.csID
) AS registered_count, (

SELECT COUNT( * ) 
FROM users
WHERE 1 
AND (
memberships LIKE CONCAT(  '%, ', cs.csID,  '%' ) 
OR memberships LIKE CONCAT(  '%', cs.csID,  ', %' ) 
OR memberships LIKE CONCAT(  '%, ', cs.csID,  ', %' ) 
OR memberships LIKE cs.csID
)
AND accessLevel =10
) AS enrollee_count


FROM (
`course_schedule` cs
)

LEFT JOIN  `course_genres` cg ON  `cg`.`cgID` =  `cs`.`csGenreId` 
LEFT JOIN  `course_definitions` cd ON  `cs`.`csCourseDefinitionId` =  `cd`.`cdID` 
having (enrollee_count + registered_count) < cs.csMaximumEnrollees


ALTER TABLE  `users` ADD FULLTEXT (
`userName` ,
`firstName` ,
`lastName` ,
`email`
);




